# EncurtaLink 🚀
### Modern URL Shortener & Analytics Platform with AI Assistant

EncurtaLink is a professional, high-performance URL shortening and analytics platform built on the robust **Yii 2 Framework (PHP)**. It offers an elegant dashboard UI, advanced link analytics (including geolocation, browser tracking, and OS statistics), campaign grouping, custom QR code generation (SVG/PNG), and an interactive AI Assistant ("Linky") powered by the **Google Gemini API** to assist users with platform navigation and URL insights.

---

## 🌟 Key Features

- **🔗 Advanced URL Shortening**
  - Create clean, custom, and secure short links instantly.
  - Automatically generate customizable aliases or unique, secure short slugs.
- **🤖 Gemini-Powered AI Assistant ("Linky")**
  - Interactive companion chatbot for guiding users and answering questions.
  - Integration with the Google Gemini API to analyze link statistics and offer guidance.
- **📊 Real-time Detailed Analytics**
  - Track total/unique clicks, scanning trends, and traffic sources.
  - Comprehensive breakdowns of user agents: browser, operating system, and device types.
  - Detailed geographic reports (country and city level detection).
- **🖼️ Smart QR Code Generator**
  - Beautiful, dynamic QR codes generated automatically for every link.
  - Downloadable formats in high-resolution vector SVG or raster PNG.
- **📂 Campaign Management**
  - Group shortened URLs into cohesive campaigns.
  - Compare performance across different marketing campaigns from a single interface.
- **📄 Exporting & Reporting**
  - Export full scan logs to CSV format.
  - Generate comprehensive PDF reports summarizing performance metrics.
- **📱 Fully Responsive UI**
  - A premium, dark-themed, and highly responsive dashboard.
  - Optimized viewport layouts tailored for mobile, tablet, and desktop views.

---

## 🛠️ Technology Stack

- **Backend Framework**: Yii 2 (PHP 8.1+)
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Styling**: Modern, custom CSS design system (dark mode, premium aesthetics)
- **AI Engine**: Google Gemini API
- **Dependencies**: Composer, PHP GD/Imagick (for QR codes), PDF generation component

---

## 📂 Project Structure

```
encurtador/
├── components/          # Custom services (Gemini API component, QR Code engine)
├── config/              # Application environment & database configurations
├── controllers/         # Web/Console action controllers (Dashboard, Redirect, etc.)
├── migrations/          # Database schema migrations & performance indices
├── models/              # Active Record schemas and search definitions
├── views/               # PHP MVC templates, layouts, and partial views
│   ├── dashboard/       # Dashboard analytics UI
│   ├── scanlog/         # Scan log tables and mobile-responsive links
│   └── layouts/         # Base HTML structures and theme templates
├── web/                 # Web root (Entry point, site.css, JS, and image assets)
└── yii                  # Yii console command utility
```

---

## 🚀 Installation & Local Development

Follow these steps to set up EncurtaLink in your development environment:

### 1. Prerequisites
Ensure you have the following installed on your machine:
- PHP >= 8.1
- Composer
- MySQL/MariaDB

### 2. Clone and Install
Clone the repository to your local web server directory and install dependencies:
```bash
git clone <repository-url>
cd encurtador
composer install
```

### 3. Database Setup
Create a new database (e.g., `encurtalink`) and configure the connection details in `config/db.php`:
```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1;dbname=encurtalink',
    'username' => 'your_db_username',
    'password' => 'your_db_password',
    'charset' => 'utf8mb4',
];
```

Run database migrations to initialize the schema and optimize indexes:
```bash
php yii migrate
```

### 4. Configure Gemini AI API Key
Obtain an API key from Google AI Studio and configure it in `config/params.php`:
```php
return [
    'gemini_api_key' => 'YOUR_GEMINI_API_KEY_HERE',
    // other parameters...
];
```

### 5. Running the Application
Spin up the built-in development server:
```bash
php yii serve
```
Access the application in your browser at: `http://localhost:8080`

---

## ⚙️ Optimization & Performance
- **Optimized Database Indexes**: Specifically tailored database index migrations (`add_indexes_to_scan_log`) to ensure rapid analytics querying, even with millions of redirection records.
- **Mobile-Responsive Breakpoints**: Enhanced layouts specifically designed to avoid column squeezing and text stacking on mobile devices.

---

## 🔒 License

This project is proprietary software. All rights reserved. 

For permissions, reproduction, or commercial usage, refer to the [LICENSE.md](LICENSE.md) file.
