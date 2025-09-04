-- Volunteer Table
CREATE TABLE Volunteer(
    volunteer_id int AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    nic VARCHAR(20),
    phone VARCHAR(20),
    email VARCHAR(100) UNIQUE,
    vol_address VARCHAR(255),
    DoB DATE,
    interest VARCHAR(50),
    password VARCHAR(255),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin
CREATE TABLE Admin(
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_name VARCHAR(50),
    admin_email VARCHAR(100) UNIQUE NOT NULL,
    admin_password VARCHAR(255) NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Organization (FIXED - Added missing comma after org_password)
CREATE TABLE Organization(
    org_id int AUTO_INCREMENT PRIMARY KEY,
    org_reg_no int NOT NULL UNIQUE,
    org_name VARCHAR(50),
    contact_person_name VARCHAR(50),
    contact_person_email VARCHAR(50) NOT NULL UNIQUE,
    contact_person_phone VARCHAR(12),
    service_type VARCHAR(30),
    org_password VARCHAR(255),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- interest
CREATE TABLE interest(
    interest_id int AUTO_INCREMENT PRIMARY KEY,
    interest_name VARCHAR(50) NOT NULL UNIQUE
);

-- volunteer interest 
CREATE TABLE vol_interest(
    volunteer_id int,
    interest_id int,
    PRIMARY KEY(volunteer_id,interest_id),
    FOREIGN KEY(volunteer_id) REFERENCES volunteer(volunteer_id) ON DELETE CASCADE,
    FOREIGN KEY(interest_id) REFERENCES interest(interest_id) ON DELETE CASCADE
);

-- user login table
CREATE TABLE user_login(
    session_id int AUTO_INCREMENT PRIMARY KEY,
    admin_id int,
    volunteer_id int,
    org_id int,
    activity_status VARCHAR(10),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(admin_id) REFERENCES Admin(admin_id) ON DELETE CASCADE,
    FOREIGN KEY(volunteer_id) REFERENCES volunteer(volunteer_id) ON DELETE CASCADE,
    FOREIGN KEY(org_id) REFERENCES Organization(org_id) ON DELETE CASCADE
);

-- events
CREATE TABLE Event(
    event_id int AUTO_INCREMENT PRIMARY KEY,
    org_id int,
    event_name VARCHAR(50) NOT NULL,
    event_time DATETIME,
    event_venue VARCHAR(50),
    description VARCHAR(255),
    category VARCHAR(30),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(org_id) REFERENCES Organization(org_id) ON DELETE CASCADE
);

-- event assignment
CREATE TABLE event_assignment(
    volunteer_id int,
    org_id int,
    assign_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activity_status VARCHAR(10),
    PRIMARY KEY(volunteer_id,org_id),
    FOREIGN KEY(volunteer_id) REFERENCES volunteer(volunteer_id) ON DELETE CASCADE,
    FOREIGN KEY(org_id) REFERENCES Organization(org_id) ON DELETE CASCADE
);