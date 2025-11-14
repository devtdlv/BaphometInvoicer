# Baphomet Invoicer

Dark, elegant, intimidatingly professional invoicing and quotes micro-app built with Laravel. This invoicing tool gives your portfolio real-world credibility with boss-level ergonomics.

## Features

### Core Features
- ✅ **Create & Send Invoices** - Generate professional invoices with customizable items
- ✅ **PDF Generation** - Export invoices and quotes as PDFs using DomPDF
- ✅ **Payment Integration** - Stripe and PayPal payment processing
- ✅ **Tax & Discount Systems** - Flexible tax rates and discount calculations (percentage or fixed)
- ✅ **Client Portal** - Secure client access to view and pay invoices
- ✅ **Invoice Status Tracking** - Track invoices from draft to paid
- ✅ **Quote Management** - Create quotes and convert them to invoices
- ✅ **Dark Theme UI** - Elegant, professional dark interface

### Advanced Features
- 📊 **Analytics Dashboard** - Revenue statistics, charts, and key metrics
- 🔍 **Advanced Search & Filtering** - Search by invoice number, client, filter by status, date range, and more
- 📧 **Email Notifications** - Automatically email invoices and quotes to clients
- 📈 **Financial Reports** - Revenue reports, client reports, and data exports (CSV)
- ⚡ **Bulk Actions** - Select multiple invoices for batch operations (mark as sent/paid, delete)
- 🔄 **Recurring Invoices** - Set up automated recurring billing (models ready)
- ⏰ **Automated Overdue Tracking** - Automatic status updates for overdue invoices
- 📋 **Export Functionality** - Export invoice data to CSV for accounting software
- 💱 **Multi-Currency Support** - Configure currency, symbols, and exchange rates per invoice or quote
- 🖨️ **Multiple PDF Templates** - Switch between classic and modern layouts
- 📎 **Invoice Attachments** - Upload and share supporting documents for each invoice
- 🔔 **Payment Reminders** - Automated reminder emails before and after due dates

## Requirements

- PHP >= 8.1
- Node.js >= 18.0
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- npm or yarn

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd BaphometInvoicer
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   
   Update your `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed the database (optional - creates test accounts and sample data)**
   ```bash
   php artisan db:seed
   ```
   
   This creates:
   - **Admin account**: `admin@example.com` / `password`
   - **Client account**: `client@example.com` / `password`
   - Sample client, invoices, and quotes for testing

8. **Build assets (for development)**
   ```bash
   npm run dev
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

   Visit `http://localhost:8000` in your browser.

   **Login with test account:**
   - Email: `admin@example.com`
   - Password: `password`

## Configuration

### Payment Gateways

Configure your payment gateways in `.env`:

**Stripe:**
```env
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret
```

**PayPal:**
```env
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_client_secret
PAYPAL_MODE=sandbox  # or 'live' for production
```

### Invoice Settings

Customize invoice prefixes and defaults:
```env
INVOICE_PREFIX=INV-
QUOTE_PREFIX=QUO-
INVOICE_DUE_DAYS=30
```

## Usage

### Creating Your First Invoice

1. **Register/Login** - Create an account or log in
2. **Add a Client** - Navigate to Clients and add your first client
3. **Create Invoice** - Go to Invoices → Create Invoice
4. **Add Items** - Add line items with descriptions, quantities, and prices
5. **Configure Tax & Discounts** - Set tax rates and apply discounts if needed
6. **Send Invoice** - Send to client or download as PDF

### Converting Quotes to Invoices

1. Create a quote with items and pricing
2. Send the quote to your client
3. Once accepted, convert it directly to an invoice

### Client Portal

Clients can:
- View their invoices
- Download PDF copies
- Pay invoices online via Stripe or PayPal

## Tech Stack

- **Backend:** Laravel 10
- **Frontend:** Blade Templates with custom dark theme
- **PDF Generation:** DomPDF
- **Payments:** Stripe, PayPal
- **Database:** MySQL
- **Asset Compilation:** Vite

## Project Structure

```
BaphometInvoicer/
├── app/
│   ├── Http/Controllers/    # Application controllers
│   ├── Models/              # Eloquent models
│   ├── Policies/            # Authorization policies
│   └── Services/            # Business logic services
├── database/
│   └── migrations/          # Database migrations
├── resources/
│   ├── views/               # Blade templates
│   │   ├── invoices/       # Invoice views
│   │   ├── quotes/         # Quote views
│   │   ├── clients/        # Client views
│   │   └── pdf/            # PDF templates
│   ├── css/                # Stylesheets
│   └── js/                 # JavaScript
├── routes/
│   ├── web.php             # Web routes
│   └── api.php             # API routes
└── config/                 # Configuration files
```

## Development

### Running Tests
```bash
php artisan test
```

### Building for Production
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Code Style
```bash
./vendor/bin/pint
```

## Security

- All user inputs are validated
- CSRF protection enabled
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating
- Password hashing with bcrypt
- Authorization policies for resource access

## License

MIT License - feel free to use this project for your portfolio or commercial projects.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

For issues and questions, please open an issue on the repository.

---

**Built with ❤️ using Laravel**
