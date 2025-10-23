
CREATE DATABASE IF NOT EXISTS freelance_db;
USE freelance_db;

-- client
CREATE TABLE client (
  client_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  phone VARCHAR(30),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- project
CREATE TABLE project (
  project_id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  project_name VARCHAR(150) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE,
  status VARCHAR(50) DEFAULT 'ongoing', -- e.g. ongoing, completed, cancelled
  total_amount DECIMAL(10,2) DEFAULT 0.00,
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES client(client_id) ON DELETE CASCADE
);


-- services
CREATE TABLE services (
  service_id INT AUTO_INCREMENT PRIMARY KEY,
  service_name VARCHAR(100) NOT NULL,
  rate DECIMAL(10,2) NOT NULL,
  description TEXT
);


-- project_services
CREATE TABLE project_services (
  ps_id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  service_id INT NOT NULL,
  quantity INT DEFAULT 1,
  line_total DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (project_id) REFERENCES project(project_id) ON DELETE CASCADE,
  FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE CASCADE
);


-- invoices
CREATE TABLE invoices (
  invoice_id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  issue_date DATE NOT NULL,
  due_date DATE NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status VARCHAR(30) DEFAULT 'unpaid', -- unpaid, paid, partial
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES project(project_id) ON DELETE CASCADE
);


-- payments
CREATE TABLE payments (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT NOT NULL,
  payment_date DATE NOT NULL,
  payment_amount DECIMAL(10,2) NOT NULL,
  payment_method VARCHAR(50),
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id) ON DELETE CASCADE
);

-- Sample clients
INSERT INTO client (name, email, phone) VALUES
('Ava Design Studio','ava@design.com','416-555-0101'),
('Green Tree Agency','hello@greentree.agency','647-555-0202'),
('Orbit Tech','contact@orbit.tech','437-555-0303');

-- Sample services
INSERT INTO services (service_name, rate, description) VALUES
('Website Design', 1200.00, 'Design and responsive layout'),
('Logo Design', 250.00, 'Brand mark and variations'),
('SEO Optimization', 600.00, 'On-page SEO and basic optimization'),
('Monthly Maintenance', 150.00, 'Site updates & backups');

-- Sample projects
INSERT INTO project (client_id, project_name, start_date, end_date, status, total_amount, notes)
VALUES
(1, 'Ava Portfolio Refresh', '2025-01-05', '2025-02-15', 'completed', 1800.00, 'Refreshed portfolio and CMS'),
(2, 'Green Tree - New Site', '2025-03-10', NULL, 'ongoing', 3000.00, 'E-commerce with booking'),
(3, 'Orbit Product Landing', '2025-05-01', '2025-06-01', 'completed', 950.00, 'Landing and analytics');

-- Link services to projects (project_services)
INSERT INTO project_services (project_id, service_id, quantity, line_total) VALUES
(1, 1, 1, 1200.00),
(1, 2, 1, 250.00),
(1, 4, 2, 300.00),
(2, 1, 1, 1200.00),
(2, 3, 1, 600.00),
(2, 4, 6, 900.00),
(3, 1, 1, 1200.00);

-- Sample invoices
INSERT INTO invoices (project_id, issue_date, due_date, amount, status) VALUES
(1, '2025-02-16','2025-03-02', 1800.00, 'paid'),
(2, '2025-04-01','2025-04-15', 1500.00, 'partial'),
(2, '2025-06-01','2025-06-15', 1500.00, 'unpaid'),
(3, '2025-06-05','2025-06-20', 950.00, 'paid');

-- Sample payments (some invoices partially paid)
INSERT INTO payments (invoice_id, payment_date, payment_amount, payment_method, notes) VALUES
(1, '2025-02-20', 1800.00, 'Bank Transfer', 'Full payment'),
(2, '2025-04-10', 500.00, 'PayPal', 'First partial'),
(4, '2025-06-10', 950.00, 'Credit Card', 'Full payment for project 3');

-- Update invoices.status logic (kept simple in sample)
UPDATE invoices SET status = 'paid' WHERE invoice_id = 1;
UPDATE invoices SET status = 'partial' WHERE invoice_id = 2;
UPDATE invoices SET status = 'unpaid' WHERE invoice_id = 3;
UPDATE invoices SET status = 'paid' WHERE invoice_id = 4;
