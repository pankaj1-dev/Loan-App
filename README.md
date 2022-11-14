# Loan-App
API For Loan-App

## Introduction
* This is the Backend API For Loan-App
* The project is taken to Laravel 8 so we can develop from the latest Laravel.

## Features
* Register, Verify Token, Login (Auth)
* Create Loan By a customer (Entering Loan amount & Loan term - weekly intervals)
* Get Loans List
* Change the loan status by Admin as "APPROVED" OR "REJECT"
* After "APPROVED" the Loan Repayment will be created as (loan amount devided by number of loan term - as weekly frequency) 
* Loan Repayment with Repayment Frequency (Weekly)

## Installation

Clone the repository

    git clone https://github.com/pankaj1-dev/Loan-App.git
Switch to the repo folder

    cd Loan-App
    
Create env file

    cp .env.example .env
    
Generate a new application key

    php artisan key:generate

Run the database migrations and Seeding (**Set the database connection in .env before migrating**)

    php artisan migrate

Run the database Seeder

    php artisan db:seed

   Install Passport Auth For API

    php artisan passport:install
    
Run the config cache

    php artisan config:cache
    
Start the local development server

    php artisan serve
    
You can now access the server at http://localhost:8000

## Additional points
The 2 users are created by seeder 
1. Admin
	Email : admin@gmail.com & Password : admin123
2. Customer 
	Email : customer@gmail.com & Password : customer123

Then after all users created by "register" api are by default role "Customer" only

## Contributing
Feel free to create any pull requests for the project. For proposing any new changes or features you want to add to the project, you can send us an email at pankajmulchandani80@gmail.com.
