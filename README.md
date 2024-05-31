# Inventory Management System API

[![Tests](https://github.com/phi-rakib/inventory-management-system-api/actions/workflows/run-tests.yml/badge.svg)](https://github.com/phi-rakib/inventory-management-system-api/actions/workflows/run-tests.yml)

## Overview

This project is an Inventory Management System API built with Laravel. It consists of various modules to handle different aspects of inventory management, including product management, sales, purchases, product transfers, user roles and permissions, reporting, settings, accounts, and a dashboard for statistics and charts.

## Modules

### 1. Product Module
- **Tables**: `category`, `brand`, `attribute`, `attribute_value`, `unit_types`, `adjustment`, `product`
- **Description**: Manages the product information including categories, brands, attributes, unit types, and adjustments.

### 2. Sales Module
- **Tables**: `order`, `order_item`, `payment`, `customer`
- **Description**: Handles customer orders, payments, and order items.

### 3. Purchase Module
- **Tables**: `supplier`, `purchase`, `purchase_item`
- **Description**: Manages supplier information, purchases, and purchase items.

### 4. Product Transfer Module
- **Tables**: `product_transfer`, `product_transfer_item`, `warehouse`, `warehouse_stock`
- **Description**: Facilitates the transfer of products between warehouses and manages warehouse stocks.

### 5. User Module
- **Tables**: `user`, `role`, `permission`
- **Description**: Manages user accounts, roles, and permissions.

### 6. Report Module
- **Description**: Generates various reports for inventory management.

### 7. Settings Module
- **Tables**: `company`, `currency`, `language`, `payment_method`
- **Description**: Manages application settings including company details, currencies, languages, and payment methods.

### 8. Accounts Module
- **Tables**: `accounts`, `deposits`, `deposits_category`, `expenses`, `expenses_category`
- **Description**: Manages financial accounts, deposits, expenses, and their categories.

### 9. Dashboard Module
- **Tables**: `stats`, `charts`
- **Description**: Provides statistics and charts for a quick overview of the inventory system.

## Installation

To install and run this project, follow these steps:

1. Clone the repository:
    ```sh
    git clone https://github.com/phi-rakib/inventory-management-system-api.git
    cd inventory-management-system-api
    ```

2. Install dependencies:
    ```sh
    composer install
    npm install
    ```

3. Set up the environment file:
    ```sh
    cp .env.example .env
    ```

4. Generate application key:
    ```sh
    php artisan key:generate
    ```

5. Configure the `.env` file with your database and other settings.

6. Run database migrations:
    ```sh
    php artisan migrate
    ```

7. Seed the database (optional):
    ```sh
    php artisan db:seed
    ```

8. Start the local development server:
    ```sh
    php artisan serve
    ```

## Contributing

If you would like to contribute to this project, please follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/YourFeature`).
3. Make your changes.
4. Commit your changes (`git commit -m 'Add some feature'`).
5. Push to the branch (`git push origin feature/YourFeature`).
6. Open a pull request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contact

For any inquiries or issues, please contact [mdrakibulhaider.int@gmail.com](mailto:mdrakibulhaider.int@gmail.com).

---

Thank you for using the Inventory Management System API!
