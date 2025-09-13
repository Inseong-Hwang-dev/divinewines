<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Table\OrdersTable;
use App\Model\Table\UsersTable;
use Cake\I18n\Date;
use Cake\I18n\Time;

/**
 * Analytics Controller
 *
 * @property OrdersTable $Orders
 * @property UsersTable $Users
 */
class AnalyticsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Orders = $this->getTableLocator()->get('Orders');
        $this->Users = $this->getTableLocator()->get('Users');
    }

    /**
     * Before filter method
     *
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $user = $this->Authentication->getIdentity();

        if ($user && $user->user_type === 'Admin') {
            $this->viewBuilder()->setLayout('admin');
        }

        if ($user->user_type !== 'Admin') {
            $this->response = $this->response->withStringBody(
                '<script>alert("You do not have permission to access this page."); window.location.href = "/";</script>'
            );
            return $this->response;
        }
    }

    /**
     * Index method - Main analytics dashboard
     *
     * @return void
     */
    public function index()
    {
        // Get date range from query parameters or default to current month
        $startDate = $this->request->getQuery('start_date') 
            ? new Date($this->request->getQuery('start_date'))
            : new Date('first day of this month');
        
        $endDate = $this->request->getQuery('end_date')
            ? new Date($this->request->getQuery('end_date'))
            : new Date('last day of this month');

        // Validate dates
        try {
            // Check if dates are valid
            if (!$startDate || !$endDate) {
                throw new \Exception('Invalid date format');
            }

            // Check if start date is before end date
            if ($startDate > $endDate) {
                throw new \Exception('Start date must be before end date');
            }

            // Check if date range is not too long (e.g., max 1 year)
            $dateDiff = $endDate->diff($startDate);
            if ($dateDiff->days > 365) {
                throw new \Exception('Date range cannot exceed 1 year');
            }

            // Check if dates are not in the future
            $today = new Date();
            if ($startDate > $today || $endDate > $today) {
                throw new \Exception('Dates cannot be in the future');
            }

        } catch (\Exception $e) {
            // If validation fails, reset to current month
            $startDate = new Date('first day of this month');
            $endDate = new Date('last day of this month');
            
            // Flash error message
            $this->Flash->error($e->getMessage());
        }

        // Previous period for comparison - properly handle month boundaries
        $daysInCurrentPeriod = $startDate->diff($endDate)->days;
        $previousStartDate = (clone $startDate)->modify('-' . $daysInCurrentPeriod . ' days');
        $previousEndDate = (clone $startDate)->modify('-1 day');

        // Calculate total revenue for current period
        $currentRevenue = $this->Orders->find()
            ->select(['total' => 'SUM(Orders.total_amount)'])
            ->where([
                'Orders.created >=' => $startDate,
                'Orders.created <=' => $endDate
            ])
            ->first()
            ->total ?? 0;

        // Calculate total revenue for previous period
        $previousRevenue = $this->Orders->find()
            ->select(['total' => 'SUM(Orders.total_amount)'])
            ->where([
                'Orders.created >=' => $previousStartDate,
                'Orders.created <=' => $previousEndDate
            ])
            ->first()
            ->total ?? 0;

        // Calculate sales growth rate
        if ($previousRevenue == 0) {
            // If there was no revenue in previous period, growth is 100%
            $salesGrowthRate = $currentRevenue > 0 ? 100 : 0;
        } else {
            $salesGrowthRate = (($currentRevenue - $previousRevenue) / $previousRevenue) * 100;
        }

        // Get total number of orders for conversion rate
        $totalOrders = $this->Orders->find()
            ->where([
                'Orders.created >=' => $startDate,
                'Orders.created <=' => $endDate
            ])
            ->count();

        // Get total number of unique customers
        $totalCustomers = $this->Users->find()
            ->where(['user_type' => 'Customer'])
            ->count();

        // Calculate conversion rate
        $conversionRate = $totalCustomers > 0 ? ($totalOrders / $totalCustomers) * 100 : 0;

        // Calculate previous period conversion rate
        $previousOrders = $this->Orders->find()
            ->where([
                'Orders.created >=' => $previousStartDate,
                'Orders.created <=' => $previousEndDate
            ])
            ->count();
        $previousCustomers = $this->Users->find()
            ->where([
                'Users.created >=' => $previousStartDate,
                'Users.created <=' => $previousEndDate,
                'Users.user_type' => 'Customer'
            ])
            ->count();
        $previousConversionRate = $previousCustomers > 0 ? ($previousOrders / $previousCustomers) * 100 : 0;
        $conversionTrend = $conversionRate - $previousConversionRate;

        // Get daily revenue data for sparkline
        $dailyRevenue = $this->Orders->find()
            ->select([
                'date' => 'DATE(Orders.created)',
                'revenue' => 'SUM(Orders.total_amount)'
            ])
            ->where([
                'Orders.created >=' => (clone $startDate)->modify('-30 days'),
                'Orders.created <=' => $endDate
            ])
            ->group(['date'])
            ->order(['date' => 'ASC'])
            ->all();

        // Get monthly revenue data for chart
        $monthlyRevenue = $this->Orders->find()
            ->select([
                'month' => 'MONTH(Orders.created)',
                'year' => 'YEAR(Orders.created)',
                'revenue' => 'SUM(Orders.total_amount)'
            ])
            ->where([
                'Orders.created >=' => (clone $startDate)->modify('-11 months'),
                'Orders.created <=' => $endDate
            ])
            ->group(['year', 'month'])
            ->order(['year' => 'ASC', 'month' => 'ASC'])
            ->all();

        $this->set(compact(
            'currentRevenue',
            'previousRevenue',
            'salesGrowthRate',
            'conversionRate',
            'conversionTrend',
            'totalOrders',
            'totalCustomers',
            'monthlyRevenue',
            'dailyRevenue',
            'startDate',
            'endDate',
            'previousStartDate',
            'previousEndDate'
        ));
    }
} 