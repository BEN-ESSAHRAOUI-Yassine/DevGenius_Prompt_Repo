CREATE DATABASE IF NOT EXISTS devgenius_db;
USE devgenius_db;

CREATE TABLE IF NOT EXISTS categories (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(100) NOT NULL,
email VARCHAR(100) NOT NULL,
password VARCHAR(200) NOT NULL,
his_role ENUM('Admin','developper') DEFAULT 'developper',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS prompts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Approved','Rejected','Deployed') DEFAULT 'Approved',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

INSERT INTO users (username, email, password, his_role) VALUES
('ShadowDrake', 'shadowdrake@email.com', '$2y$10$C/gMqdZbARRGmnjFIYiM/.1Jh0wVyNJvtvQ0O34fG6mFOviQTkzEq', 'Admin'), /*dragon123*/
('LunarWizard', 'lunarwizard@email.com', '$2y$10$YkW6C51zTZlb043ZZb2s6ujjOeKEFJ5YWomRjZhokdhlnycLPJihW', 'developper'), /*moonmagic*/
('IronKnight', 'ironknight@email.com', '$2y$10$jywqEIwj3uhS1a2NizJMtumxQ1hHmnhSO.kSGEHEPuhG.tn000.y6', 'developper'), /*sword456*/
('FrostPhoenix', 'frostphoenix@email.com', '$2y$10$Nm1D4ZyB6gATJKadzQmTiOxe.tPQEwrvptOuJjdpClKW2UL5AzCUe', 'developper'), /*icefire789*/
('MysticRanger', 'mysticranger@email.com', '$2y$10$IYLj7N9zgovbRR8JpdwT6Oe95PBeITwU3Un6.beOExiQt.ypeKate', 'developper'); /*forest999*/

INSERT INTO categories (name) VALUES
('Code'),
('Marketing'),
('DevOps'),
('SQL'),
('UI/UX'),
('Testing'),
('AI'),
('Security');

INSERT INTO prompts (title, content, user_id, category_id, status) VALUES
('Build secure login system', 'Create a secure authentication system in PHP using PDO, sessions, and password hashing.', 3, 1, 'Approved'),
('REST API structure', 'Generate a clean REST API architecture in PHP with controllers and models.', 5, 1, 'Approved'),
('Form validation PHP', 'Validate user input server-side and return clear error messages.', 2, 1, 'Approved'),
('File upload security', 'Handle file uploads securely with MIME type validation and size limits.', 4, 1, 'Approved'),
('Pagination system', 'Implement pagination for large datasets using LIMIT and OFFSET.', 1, 1, 'Approved'),
('MVC refactor', 'Refactor procedural PHP code into MVC architecture.', 5, 1, 'Deployed'),
('Error handling', 'Implement global error handling and logging system.', 1, 1, 'Approved'),
('JSON API response', 'Return structured JSON responses for API endpoints.', 3, 1, 'Approved'),
('Session security', 'Secure session handling to prevent hijacking.', 4, 1, 'Approved'),
('Reusable components', 'Create reusable PHP components for forms and tables.', 1, 1, 'Approved'),
('Landing page copy', 'Write a high-converting landing page for a SaaS product.', 5, 2, 'Approved'),
('Email campaign', 'Generate a persuasive email marketing campaign.', 2, 2, 'Approved'),
('SEO blog ideas', 'Suggest SEO-friendly blog post ideas for developers.', 4, 2, 'Approved'),
('Product description', 'Write engaging product descriptions for a tech store.', 3, 2, 'Approved'),
('CTA optimization', 'Improve call-to-action buttons for conversions.', 1, 2, 'Approved'),
('Brand storytelling', 'Create a compelling brand story for a startup.', 5, 2, 'Deployed'),
('Social media posts', 'Generate weekly social media content.', 3, 2, 'Approved'),
('Ad copywriting', 'Write Facebook and Google Ads copy.', 4, 2, 'Approved'),
('Marketing funnel', 'Design a full customer journey funnel.', 1, 2, 'Approved'),
('Newsletter content', 'Create engaging newsletter content.', 2, 2, 'Approved'),
('Docker setup PHP', 'Create a Docker environment for PHP and MySQL.', 3, 3, 'Approved'),
('CI/CD pipeline', 'Set up CI/CD using GitHub Actions.', 5, 3, 'Approved'),
('Nginx config', 'Configure Nginx for a PHP application.', 2, 3, 'Approved'),
('Deploy VPS', 'Deploy application on a Linux VPS.', 1, 3, 'Approved'),
('Log monitoring', 'Set up log monitoring system.', 5, 3, 'Approved'),
('SSL setup', 'Install SSL certificate using Let’s Encrypt.', 4, 3, 'Approved'),
('Environment config', 'Manage environment variables securely.', 2, 3, 'Approved'),
('Database backup', 'Automate MySQL backups.', 3, 3, 'Approved'),
('Scaling strategy', 'Design horizontal scaling architecture.', 1, 3, 'Rejected'),
('Server hardening', 'Secure a Linux server for production.', 5, 3, 'Approved'),
('INNER JOIN query', 'Write SQL query using INNER JOIN between users and prompts.', 4, 4, 'Approved'),
('Optimize query', 'Optimize slow SQL queries using indexes.', 1, 4, 'Approved'),
('Database design', 'Design a normalized relational database schema.', 3, 4, 'Approved'),
('Aggregate functions', 'Use COUNT, SUM, AVG effectively.', 2, 4, 'Approved'),
('Subquery usage', 'Write efficient subqueries.', 5, 4, 'Approved'),
('Transactions SQL', 'Implement transactions with rollback.', 2, 4, 'Approved'),
('Foreign keys', 'Use foreign keys to enforce integrity.', 1, 4, 'Approved'),
('Group by', 'Group and filter aggregated data.', 4, 4, 'Approved'),
('Query debugging', 'Debug incorrect SQL queries.', 3, 4, 'Approved'),
('Data migration', 'Safely migrate database data.', 5, 4, 'Approved'),
('Dashboard design', 'Design a clean admin dashboard UI.', 2, 5, 'Approved'),
('Form UX', 'Improve usability of forms.', 3, 5, 'Approved'),
('Color system', 'Define a modern color palette.', 1, 5, 'Approved'),
('Typography', 'Select readable fonts for web apps.', 4, 5, 'Approved'),
('Responsive layout', 'Make layout responsive using CSS.', 3, 5, 'Approved'),
('UX audit', 'Analyze usability issues.', 1, 5, 'Approved'),
('Wireframe creation', 'Create wireframes for new feature.', 5, 5, 'Approved'),
('Dark mode UI', 'Implement dark mode.', 4, 5, 'Approved'),
('Accessibility', 'Improve accessibility (ARIA).', 2, 5, 'Approved'),
('Component system', 'Build reusable UI components.', 5, 5, 'Approved'),
('Unit testing PHP', 'Write PHPUnit tests for backend.', 3, 6, 'Approved'),
('API testing', 'Test REST API endpoints.', 2, 6, 'Approved'),
('Debugging errors', 'Debug PHP runtime errors.', 5, 6, 'Approved'),
('Test coverage', 'Increase test coverage.', 1, 6, 'Approved'),
('Mock data', 'Generate test data.', 3, 6, 'Approved'),
('Automation tests', 'Automate tests pipeline.', 4, 6, 'Approved'),
('Regression testing', 'Perform regression testing.', 1, 6, 'Approved'),
('Test cases', 'Write detailed test cases.', 3, 6, 'Approved'),
('Bug tracking', 'Track bugs effectively.', 5, 6, 'Approved'),
('Performance test', 'Measure application performance.', 4, 6, 'Approved'),
('Prompt engineering', 'Improve AI prompts for better outputs.', 2, 7, 'Approved'),
('Chatbot design', 'Design chatbot responses.', 1, 7, 'Approved'),
('Text summarization', 'Summarize long articles.', 3, 7, 'Approved'),
('AI code generation', 'Generate backend code using AI.', 5, 7, 'Approved'),
('Sentiment analysis', 'Analyze sentiment in text.', 4, 7, 'Approved'),
('AI assistant', 'Build personal AI assistant.', 3, 7, 'Approved'),
('Translation AI', 'Translate text automatically.', 2, 7, 'Approved'),
('Text classification', 'Classify documents.', 1, 7, 'Approved'),
('Data extraction', 'Extract structured data.', 4, 7, 'Approved'),
('Workflow automation', 'Automate workflows using AI.', 5, 7, 'Approved'),
('Password hashing', 'Secure passwords using bcrypt.', 3, 8, 'Approved'),
('SQL injection prevention', 'Prevent SQL injection using prepared statements.', 1, 8, 'Approved'),
('XSS protection', 'Sanitize outputs to prevent XSS.', 2, 8, 'Approved'),
('CSRF protection', 'Implement CSRF tokens.', 4, 8, 'Approved'),
('Data encryption', 'Encrypt sensitive data.', 5, 8, 'Approved'),
('Secure headers', 'Add HTTP security headers.', 3, 8, 'Approved'),
('Authentication security', 'Secure login system.', 2, 8, 'Approved'),
('Session hijacking', 'Prevent session hijacking.', 4, 8, 'Approved'),
('Input sanitization', 'Sanitize all inputs.', 1, 8, 'Approved'),
('Access control', 'Implement role-based access control.', 5, 8, 'Approved');