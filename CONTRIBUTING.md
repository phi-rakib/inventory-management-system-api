# Contributing to Inventory Management System API

First off, thank you for considering contributing to our project! Your involvement is greatly appreciated. This document provides guidelines to help you contribute effectively.

## Table of Contents

1. [Code of Conduct](#code-of-conduct)
2. [How Can I Contribute?](#how-can-i-contribute)
    - [Reporting Bugs](#reporting-bugs)
    - [Suggesting Features](#suggesting-features)
    - [Submitting Pull Requests](#submitting-pull-requests)
3. [Development Setup](#development-setup)
4. [Style Guides](#style-guides)
    - [Git Commit Messages](#git-commit-messages)
    - [PHP Code Style](#php-code-style)
    - [JavaScript Code Style](#javascript-code-style)
5. [Additional Notes](#additional-notes)

## Code of Conduct

This project and everyone participating in it is governed by the [Contributor Covenant Code of Conduct](https://github.com/phi-rakib/inventory-management-system-api/blob/main/CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs

If you find a bug, please report it by opening an issue in our [GitHub Issues](https://github.com/phi-rakib/inventory-management-system-api/issues) with the following details:

- **Summary**: A clear and concise description of the bug.
- **Steps to Reproduce**: A step-by-step guide on how to reproduce the issue.
- **Expected Result**: What you expected to happen.
- **Actual Result**: What actually happened.
- **Screenshots or Screencasts**: If applicable, add any visual aids.

### Suggesting Features

We welcome feature suggestions. To suggest a feature:

1. **Search**: First, check the [existing issues](https://github.com/phi-rakib/inventory-management-system-api/issues) to ensure your suggestion hasn’t been made before.
2. **New Issue**: Open a new issue, and provide as much detail as possible:
    - **Describe the feature**: Explain the feature and its benefits.
    - **Motivation**: Why do you want this feature?
    - **Alternatives**: Any alternative solutions you have considered.

### Submitting Pull Requests

1. **Fork the Repository**: Create a fork of the repository by clicking the "Fork" button at the top of the project page.
2. **Clone Your Fork**: Clone your forked repository to your local machine.
    ```bash
    git clone https://github.com/phi-rakib/inventory-management-system-api.git
    ```
3. **Create a Branch**: Always create a new branch for your work.
    ```bash
    git checkout -b feature/your-feature-name
    ```
4. **Commit Your Changes**: Make your changes and commit them with clear and descriptive messages.
5. **Push to Your Fork**: Push your branch to your fork.
    ```bash
    git push origin feature/your-feature-name
    ```
6. **Open a Pull Request**: Navigate to the original repository and open a pull request from your branch.

Please ensure your pull request adheres to the following:
- **Description**: Provide a detailed description of what your PR does.
- **Related Issues**: Mention any related issues (e.g., “Closes #123”).
- **Tests**: Include any tests that are relevant.

## Development Setup

1. **Clone the Repository**: 
    ```bash
    git clone https://github.com/phi-rakib/inventory-management-system-api.git
    cd your-project
    ```
2. **Install Dependencies**: Use Composer and npm to install dependencies.
    ```bash
    composer install
    npm install
    ```
3. **Set Up Environment**: Copy `.env.example` to `.env` and configure your environment variables.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4. **Run Migrations**: Set up the database by running migrations.
    ```bash
    php artisan migrate
    ```
5. **Start the Server**: Run the development server.
    ```bash
    php artisan serve
    ```

## Style Guides

### Git Commit Messages

- **Format**: Use the present tense (“Add feature” not “Added feature”).
- **Style**: Keep messages concise but descriptive.

### PHP Code Style

- Follow the [PSR-4 Coding Standard](https://www.php-fig.org/psr/psr-4/).

### JavaScript Code Style

- Follow the [Airbnb JavaScript Style Guide](https://github.com/airbnb/javascript).

## Additional Notes

- **Documentation**: Ensure any relevant documentation is updated.
- **Tests**: If you’re adding new features, make sure to include tests.

Thank you for your contribution!
