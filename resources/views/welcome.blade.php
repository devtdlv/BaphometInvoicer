<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Baphomet Invoicer') }} - Professional Invoicing Solution</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg-primary: #f5f7fb;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f1f3f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --accent: #5b21b6;
            --accent-hover: #4c1d95;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #dc2626;
            --warning: #f59e0b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-primary {
            background: var(--accent);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--accent-hover);
        }
        
        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: var(--bg-secondary);
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-menu-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-primary);
            font-size: 0.875rem;
        }
        
        .user-menu-button:hover {
            background: var(--bg-tertiary);
            border-color: var(--accent);
        }
        
        .user-menu-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.15);
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s;
            z-index: 1000;
        }
        
        .user-menu-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .user-menu-item {
            display: block;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.875rem;
            transition: background 0.2s;
            border: none;
            width: 100%;
            text-align: left;
            background: transparent;
            cursor: pointer;
        }
        
        .user-menu-item:first-child {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        
        .user-menu-item:last-child {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
        
        .user-menu-item:hover {
            background: var(--bg-tertiary);
        }
        
        .user-menu-item.logout {
            color: var(--danger);
            border-top: 1px solid var(--border);
        }
        
        .user-menu-item.logout:hover {
            background: rgba(239, 68, 68, 0.1);
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .dropdown-arrow {
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid var(--text-secondary);
            transition: transform 0.2s;
        }
        
        .user-menu-button.active .dropdown-arrow {
            transform: rotate(180deg);
        }
        
        .nav-link {
            transition: all 0.2s ease;
        }
        
        .nav-link:hover {
            background: rgba(91, 33, 182, 0.05) !important;
            color: var(--accent) !important;
        }
        
        /* Hero Section */
        .hero {
            padding: 5rem 0;
            background: linear-gradient(to bottom, var(--bg-secondary), var(--bg-primary));
        }
        
        .hero-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
        
        .hero-cta {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Features Section */
        .features {
            padding: 5rem 0;
            background: var(--bg-primary);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .section-header p {
            font-size: 1.125rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .feature-card {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 2rem;
            transition: all 0.3s;
        }
        
        .feature-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-4px);
        }
        
        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(91, 33, 182, 0.1);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
        }
        
        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        /* Tech Stack Section */
        .tech-stack {
            padding: 5rem 0;
            background: var(--bg-secondary);
        }
        
        .tech-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 3rem;
        }
        
        .tech-badge {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            color: var(--text-primary);
            transition: all 0.2s;
        }
        
        .tech-badge:hover {
            border-color: var(--accent);
            color: var(--accent);
        }
        
        /* CTA Section */
        .cta-section {
            padding: 5rem 0;
            background: var(--bg-primary);
            text-align: center;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .cta-section p {
            font-size: 1.125rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }
        
        /* Footer */
        .footer {
            background: var(--bg-secondary);
            border-top: 1px solid var(--border);
            padding: 2rem 0;
            text-align: center;
            color: var(--text-secondary);
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.125rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <x-header />

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Professional Invoicing Made Simple</h1>
                <p>Create, send, and manage invoices with ease. Track payments, generate reports, and streamline your billing process.</p>
                <div class="hero-cta">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">Go to Dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">Get Started Free</a>
                        <a href="{{ route('login') }}" class="btn btn-secondary" style="padding: 0.875rem 2rem; font-size: 1rem;">Sign In</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2>Everything You Need</h2>
                <p>Powerful features to manage your invoicing workflow efficiently</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📄</div>
                    <h3>Create & Send Invoices</h3>
                    <p>Generate professional invoices with customizable items, tax rates, and discounts. Send them directly to clients via email.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Payment Processing</h3>
                    <p>Accept payments online through Stripe and PayPal. Track payment status and automatically update invoice records.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Analytics & Reports</h3>
                    <p>View revenue statistics, track overdue invoices, and generate detailed financial reports. Export data to CSV for accounting.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Client Management</h3>
                    <p>Manage your client database, track payment history, and provide secure client portal access for invoice viewing.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📋</div>
                    <h3>Quote Management</h3>
                    <p>Create professional quotes and convert them to invoices with a single click. Track quote status and acceptance.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌍</div>
                    <h3>Multi-Currency Support</h3>
                    <p>Work with multiple currencies, set exchange rates, and customize currency symbols for international clients.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📎</div>
                    <h3>Document Attachments</h3>
                    <p>Attach supporting documents to invoices. Share contracts, receipts, or any relevant files with your clients.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔔</div>
                    <h3>Automated Reminders</h3>
                    <p>Set up automated payment reminders for overdue invoices. Never miss a payment with scheduled email notifications.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🖨️</div>
                    <h3>PDF Generation</h3>
                    <p>Export invoices and quotes as professional PDFs. Choose from multiple templates to match your brand.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack Section -->
    <section class="tech-stack">
        <div class="container">
            <div class="section-header">
                <h2>Built With Modern Technology</h2>
                <p>Powered by industry-leading frameworks and tools</p>
            </div>
            <div class="tech-grid">
                <div class="tech-badge">Laravel 10</div>
                <div class="tech-badge">PHP 8.1+</div>
                <div class="tech-badge">MySQL</div>
                <div class="tech-badge">DomPDF</div>
                <div class="tech-badge">Stripe</div>
                <div class="tech-badge">PayPal</div>
                <div class="tech-badge">Blade Templates</div>
                <div class="tech-badge">Vite</div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of businesses streamlining their invoicing process</p>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">Go to Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">Create Your Account</a>
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Built with Laravel.</p>
        </div>
    </footer>
    
    <script>
        // User menu dropdown toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.getElementById('userMenuButton');
            const menuDropdown = document.getElementById('userMenuDropdown');
            
            if (menuButton && menuDropdown) {
                menuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    menuButton.classList.toggle('active');
                    menuDropdown.classList.toggle('active');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!menuButton.contains(e.target) && !menuDropdown.contains(e.target)) {
                        menuButton.classList.remove('active');
                        menuDropdown.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>

