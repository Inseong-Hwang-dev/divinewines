<img width="1377" height="750" alt="divinevines-cover" src="https://github.com/user-attachments/assets/ebd827d1-dd66-4695-8804-9259d60fb24d" />

# DivineVines

DivineVines is an e-commerce web application built for an Australian wine business selling products internationally.

The platform was designed to support the full online purchasing flow, from browsing wine products to account-based checkout, international shipping calculation, and secure payment processing.

## Overview

Buying wine internationally can involve extra steps such as shipping cost calculation, address validation, postcode compatibility checks, and secure payment handling.

DivineVines was built to bring these steps into one checkout flow, helping customers place international orders more smoothly while reducing manual work for the business.

## Key Features

- Browse wine products and view product details
- User account and authentication workflows
- Shopping cart and checkout flow
- International shipping cost calculation using the Australia Post API
- Address validation and postcode compatibility checks
- Secure payment processing through Stripe
- Database-backed order and customer workflows
- MVC-based application structure using CakePHP

## Tech Stack

- **Backend:** PHP, CakePHP
- **Database:** MySQL
- **APIs:** Australia Post API, Stripe API
- **Architecture:** MVC
- **Collaboration:** GitHub, Agile team workflow

## My Contribution

I worked as the **Main Software Developer and Team Lead** in a team of five.

My main responsibilities included:

- Leading backend development and helping establish a shared CakePHP MVC structure for the team
- Designing checkout-related workflows for orders, shipping, payments, and account-based user flows
- Integrating the Australia Post API to calculate international shipping costs during checkout
- Implementing address validation and postcode compatibility checks to reduce checkout errors
- Integrating Stripe to support secure payment processing
- Coordinating technical decisions and keeping development workflows consistent across the project

## What I Learned

This project gave me practical experience building features that need to work together in a real e-commerce flow.

In particular, I learned how to:

- Design backend workflows around real user actions instead of isolated features
- Integrate third-party APIs into a production-style checkout process
- Handle validation and edge cases that affect order accuracy
- Work within a shared MVC codebase as part of a development team
- Balance technical implementation with the business needs of an online store

## Screenshots

> Add screenshots here showing the product browsing page, cart, checkout flow, shipping calculation, and payment screen.

<!--
![Product Page](./screenshots/product-page.png)
![Checkout](./screenshots/checkout.png)
![Shipping Calculation](./screenshots/shipping.png)
-->

## Project Context

This project was developed as a university team project at Monash University.

# CakePHP Application Skeleton

![Build Status](https://github.com/cakephp/app/actions/workflows/ci.yml/badge.svg?branch=5.x)
[![Total Downloads](https://img.shields.io/packagist/dt/cakephp/app.svg?style=flat-square)](https://packagist.org/packages/cakephp/app)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

A skeleton for creating applications with [CakePHP](https://cakephp.org) 5.x.

The framework source code can be found here: [cakephp/cakephp](https://github.com/cakephp/cakephp).

## Installation

1. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `php composer.phar create-project --prefer-dist cakephp/app [app_name]`.

If Composer is installed globally, run

```bash
composer create-project --prefer-dist cakephp/app
```

In case you want to use a custom app dir name (e.g. `/myapp/`):

```bash
composer create-project --prefer-dist cakephp/app myapp
```

You can now either use your machine's webserver to view the default home page, or start
up the built-in webserver with:

```bash
bin/cake server -p 8765
```

Then visit `http://localhost:8765` to see the welcome page.

## Update

Since this skeleton is a starting point for your application and various files
would have been modified as per your needs, there isn't a way to provide
automated upgrades, so you have to do any updates manually.

## Configuration

Read and edit the environment specific `config/app_local.php` and set up the
`'Datasources'` and any other configuration relevant for your application.
Other environment agnostic settings can be changed in `config/app.php`.

## Layout

The app skeleton uses [Milligram](https://milligram.io/) (v1.3) minimalist CSS
framework by default. You can, however, replace it with any other library or
custom styles.
