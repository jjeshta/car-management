# Car Management Application

## Overview

The Car Management Application is designed to help users manage their car information efficiently. It allows users to perform various operations such as adding, updating, and removing car details, while maintaining a clear log of all activities.

## Architecture

### Domain-Driven Design (DDD)

- **Focus on the Core Domain**: By modeling the application around the domain of car management, we can accurately represent business processes, ensuring that the application provides real value to users.
- **Encourage Collaboration**: DDD promotes collaboration between domain experts and developers, resulting in a shared understanding of requirements and clearer communication.
- **Adapt to Changing Requirements**: The domain model is designed to be flexible, making it easier to accommodate changes in business logic and requirements over time.
- **Adheres to SOLID**

### Command Query Responsibility Segregation (CQRS)

The application also implements Command Query Responsibility Segregation (CQRS) to separate the responsibilities of reading and writing data. This approach offers several advantages:

- **Scalability**: By decoupling read and write operations, the application can scale more effectively to handle varying loads, allowing independent optimization of read and write pathways.
- **Performance Optimization**: Different data models can be used for reading and writing, enabling optimizations specific to each operation type, which enhances overall application performance.
- **Simplified Complexity**: CQRS simplifies the management of complex business logic by allowing different models to be used for commands (updates) and queries (reads), leading to cleaner and more maintainable code.

By leveraging DDD and CQRS, the Car Management Application is structured to deliver a robust and scalable solution that meets both current and future business needs.


## Features

- **Add Car**: Easily add new car information, including make, model, and registration number.
- **Update Car**: Update existing car details to keep the information current.
- **Remove Car**: Remove car entries from the system when they are no longer needed.
- **Manage Service History**: 
  - **Add Service Record**: Keep track of service history for each car.
  - **Remove Service Record**: Easily remove service history entries when they are no longer relevant.
- **Logging**: Detailed logging of operations to track changes and errors.

## Technologies Used

- **PHP**: Backend programming language.
- **Symfony**: Framework used for developing the application.
- **Doctrine**: ORM for database interaction.
- **PHPUnit**: Testing framework for unit and integration testing.

## Logging

Log files are available outside the project directory to ensure easier access. These log files are organized and separated by different names based on the nature of the messages (errors, info.), allowing for efficient monitoring and troubleshooting.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/car-management-app.git
    ```
2. Navigate to the project directory:
    ```bash
    cd car-management-app
    ```
3. Configure your database settings in the .env file. and make sure the `./logs` folder has the write permissions if not do a chown
    ```
    chown -R {USER}:{USER} ./logs
    ```     
    Replace {USER} with your username.

4. Build the Docker containers and start the application in detached mode
  ```bash
    docker-compose build
    ```
    and 
    ```bash
    docker-compose up -d
    ```
5. Access the PHP container:
 ```bash
    docker exec -it <php-container-name> bash

    ```

6. Do the composer install in the php container  
 ```bash
    composer install
    ```


## List of APIs

### Car Management APIs

1. **Add Car**
Note: local address is: http://localhost:8000/
   - **Endpoint**: `POST /api/cars`
   - **Description**: Adds a new car to the system.
   - **Request Body**:
     ```json
     {
       "make": "string",
       "model": "string",
       "registrationNumber": "string",
       "insurance": {
         "insurer": "string",
         "policyNumber": "string",
         "dateIssued": "string (YYYY-MM-DD HH:MM:SS)",
         "dateExpiry": "string (YYYY-MM-DD HH:MM:SS)",
         "dateStart": "string (YYYY-MM-DD HH:MM:SS)"
       },
       "fitness": {
         "issued": "string (YYYY-MM-DD HH:MM:SS)",
         "validUntil": "string (YYYY-MM-DD HH:MM:SS)"
       },
       "roadTax": {
         "issued": "string (YYYY-MM-DD HH:MM:SS)",
         "validUntil": "string (YYYY-MM-DD HH:MM:SS)"
       }
     }
     ```
   - **Example Request**:
     ```json
     {
       "make": "Mercedes",
       "model": "Benz",
       "registrationNumber": "6597 EZ 19",
       "insurance": {
         "insurer": "InsureCo",
         "policyNumber": "3534656573",
         "dateIssued": "2024-10-12 00:00:00",
         "dateExpiry": "2025-01-01 00:00:00",
         "dateStart": "2024-12-31 00:00:00"
       },
       "fitness": {
         "issued": "2024-07-01 23:44:00",
         "validUntil": "2029-01-01 23:44:00"
       },
       "roadTax": {
         "issued": "2024-01-01 23:44:00",
         "validUntil": "2025-05-01 23:44:00"
       }
     }
     ```
   - **Response**: Success message with the added car details.


2. **Patch Car**
   - **Endpoint**: `PATCH /api/cars/{registrationNumber}`
   - **Description**: Updates the `make` and `model` of an existing car.
   - **Request Body** (only include fields you want to update):
     ```json
     {
       "make": "string",
       "model": "string"
     }
     ```
   - **Example Request**:
     ```json
     {
       "make": "Mercedes",
       "model": "Benz Updated"
     }
     ```
   - **Response**: Success message with updated car details.

    Example Request:
    To update the car with registration number `2376 EZ 15`, you would make a request to: http://localhost:8000/api/cars/2376%20EZ%2015
    with body:
    ```json
    {
        "make": "NewMake",
        "model": "NewModel"
    }
    ```json

    3. **Get Car Details**
   - **Endpoint**: `GET /api/cars/{registrationNumber}`
   - **Description**: Retrieves details of a specific car by its registration number.
   - **Response**:
     ```json
     {
       "make": "string",
       "model": "string",
       "registrationNumber": "string",
       "insurance": {
         "insurer": "string",
         "policyNumber": "string",
         "dateIssued": "string (YYYY-MM-DD HH:MM:SS)",
         "dateExpiry": "string (YYYY-MM-DD HH:MM:SS)",
         "dateStart": "string (YYYY-MM-DD HH:MM:SS)"
       },
       "fitness": {
         "issued": "string (YYYY-MM-DD HH:MM:SS)",
         "validUntil": "string (YYYY-MM-DD HH:MM:SS)"
       },
       "roadTax": {
         "issued": "string (YYYY-MM-DD HH:MM:SS)",
         "validUntil": "string (YYYY-MM-DD HH:MM:SS)"
       }
     }
     ```
   - **Example Request**:
     To retrieve the car with registration number `2376 EZ 15`, you would make a request to:
     ```
     http://localhost:8000/api/cars/2376%20EZ%2015
     ```

4. **Get Unfit Cars**
   - **Endpoint**: `GET /api/cars/unfit`
   - **Description**: Retrieves a list of cars that are unfit.
   - **Response**:
    ```json
     [
       {
         "id": 2,
         "make": "Toyota",
         "model": "Camry",
         "registrationNumber": "2342 ZZ 11",
         "insurance": {
           "insurer": "InsureCo",
           "policyNumber": "123456789",
           "dateIssued": "2024-01-01T00:00:00+00:00",
           "dateExpiry": "2025-01-01T00:00:00+00:00",
           "dateStart": "2024-01-01T00:00:00+00:00",
           "valid": true
         },
         "fitness": {
           "issued": "2023-07-01T23:44:00+00:00",
           "validUntil": "2024-01-01T23:44:00+00:00",
           "valid": false
         },
         "roadTax": {
           "issued": "2025-01-01T23:44:00+00:00",
           "validUntil": "2025-05-01T23:44:00+00:00",
           "valid": true
         },
         "serviceHistories": [],
         "fitForRoad": false
       }
     ]
     ```
   - **Example Request**:
     To retrieve all unfit cars, you would make a request to:
     ```
     http://localhost:8000/api/cars/unfit
     ```

5. **Get Fit Cars**
   - **Endpoint**: `GET /api/cars/fit`
   - **Description**: Retrieves a list of cars that are fit.
   - **Response**:
    ```json
     [
       {
         "id": 2,
         "make": "Toyota",
         "model": "Camry",
         "registrationNumber": "2342 ZZ 11",
         "insurance": {
           "insurer": "InsureCo",
           "policyNumber": "123456789",
           "dateIssued": "2024-01-01T00:00:00+00:00",
           "dateExpiry": "2025-01-01T00:00:00+00:00",
           "dateStart": "2024-01-01T00:00:00+00:00",
           "valid": true
         },
         "fitness": {
           "issued": "2023-07-01T23:44:00+00:00",
           "validUntil": "2024-01-01T23:44:00+00:00",
           "valid": true
         },
         "roadTax": {
           "issued": "2025-01-01T23:44:00+00:00",
           "validUntil": "2025-05-01T23:44:00+00:00",
           "valid": true
         },
         "serviceHistories": [],
         "fitForRoad": true
       }
     ]
     ```
   - **Example Request**:
     To retrieve all fit cars, you would make a request to:
     ```
     http://localhost:8000/api/cars/fit
     ```

6. **Delete a Car**
   - **Endpoint**: `DELETE /api/car/{registrationNumber}`
   - **Description**: Deletes a car by its registration number.
   - **Example Request**:
     To delete the car with registration number `1411 EZ 31`, you would make a request to:
     ```http
     DELETE http://localhost:8000/api/car/1411%20EZ%2031
     ```

7. **Create Service History**
   - **Endpoint**: `POST /api/service-history`
   - **Description**: Creates a new service history record.
   - **Request Body**: (Example)
     ```json
     {
       "carRegistrationNumber": 1,
       "serviceDate": "2024-10-12",
       "details": "Oil change and tire rotation"
     }
     ```

8. **Delete a Service History**
   - **Endpoint**: `DELETE /api/service-history/{id}`
   - **Description**: Deletes a service history record by its ID.
   - **Example Request**:
     To delete the service history with ID `2`, you would make a request to:
     ```http
     DELETE http://localhost:8000/api/service-history/2
     ```