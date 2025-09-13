<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;

class BirthdayCouponsCommand extends Command
{
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('Generate birthday coupons for users')
            ->addOption('month', [
                'short' => 'm',
                'help' => 'Month to check for birthdays (1-12)',
                'required' => false
            ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $month = $args->getOption('month');
        if (!$month) {
            $month = (int)FrozenDate::now()->format('m');
        }

        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $couponsTable = TableRegistry::getTableLocator()->get('Coupons');

        // Find users with birthdays this month
        $users = $usersTable->find()
            ->where([
                'MONTH(birthday)' => $month,
                'archived' => 0
            ])
            ->all();

        $io->out(sprintf('Found %d users with birthdays in month %d', $users->count(), $month));

        foreach ($users as $user) {
            // Check if user already has a birthday coupon this year
            $existingCoupon = $couponsTable->find()
                ->where([
                    'user_id' => $user->id,
                    'coupon_code LIKE' => 'BDAY%',
                    'created >=' => FrozenDate::now()->startOfYear()->format('Y-m-d')
                ])
                ->first();

            if (!$existingCoupon) {
                // Generate unique coupon code
                $couponCode = 'BDAY' . strtoupper(substr(md5(uniqid()), 0, 6));

                // Create birthday coupon
                $coupon = $couponsTable->newEmptyEntity();
                $coupon = $couponsTable->patchEntity($coupon, [
                    'coupon_code' => $couponCode,
                    'discount_amount' => 15, // 15% discount
                    'user_id' => $user->id,
                    'is_active' => true,
                    'expiry_date' => FrozenDate::now()->addMonth(), // Valid for 1 month
                    'min_purchase_amount' => 50, // Minimum $50 purchase
                    'max_discount_amount' => 100, // Maximum $100 discount
                    'usage_limit' => 1, // Can only be used once
                    'times_used' => 0
                ]);

                if ($couponsTable->save($coupon)) {
                    $io->success(sprintf(
                        'Created birthday coupon for user %s (ID: %d)',
                        $user->email,
                        $user->id
                    ));
                } else {
                    $io->error(sprintf(
                        'Failed to create birthday coupon for user %s (ID: %d)',
                        $user->email,
                        $user->id
                    ));
                }
            } else {
                $io->out(sprintf(
                    'User %s (ID: %d) already has a birthday coupon this year',
                    $user->email,
                    $user->id
                ));
            }
        }

        return Command::CODE_SUCCESS;
    }
} 