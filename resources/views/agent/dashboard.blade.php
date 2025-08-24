<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - Laundry Service</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
        }

        .navbar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .navbar-nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }

        .logout-btn {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .welcome-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }

        .welcome-section h1 {
            color: #28a745;
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }

        .welcome-section p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: #28a745;
            margin-bottom: 1rem;
        }

        .stat-card h3 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: #666;
            font-size: 1rem;
        }

        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .quick-actions h2 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: #28a745;
            color: white;
            border-color: #28a745;
            transform: translateY(-3px);
        }

        .action-btn i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .action-btn span {
            font-weight: 600;
            text-align: center;
        }

        @media (max-width: 768px) {
            .navbar-content {
                flex-direction: column;
                gap: 1rem;
            }

            .navbar-nav {
                flex-direction: column;
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand">
                <i class="fas fa-user-tie"></i> Agent Dashboard
            </div>
            <div class="navbar-nav">
                <a href="#" class="nav-link">Profile</a>
                <a href="#" class="nav-link">Orders</a>
                <a href="#" class="nav-link">Services</a>
                <a href="#" class="nav-link">Reports</a>
                <form method="POST" action="{{ route('agent.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome, {{ Auth::user()->name }}!</h1>
            <p>Manage your laundry services and track your performance</p>
            <div class="status-badge {{ Auth::user()->status === 'active' ? 'status-active' : (Auth::user()->status === 'pending' ? 'status-pending' : 'status-inactive') }}">
                <i class="fas fa-circle"></i>
                Status: {{ ucfirst(Auth::user()->status) }}
            </div>
        </div>

        <div class="status-info">
            <h4>حالة الحساب:</h4>
            @if(auth()->user()->status === 'pending')
                <div class="alert alert-warning">
                    <i class="fas fa-clock"></i>
                    حسابك قيد المراجعة. يرجى الانتظار حتى يتم الموافقة عليه من قبل المدير.
                </div>
            @elseif(auth()->user()->status === 'approved')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    تمت الموافقة على حسابك. يمكنك الآن استخدام جميع الميزات.
                </div>
            @elseif(auth()->user()->status === 'rejected')
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i>
                    تم رفض حسابك. يرجى التواصل مع المدير للمزيد من المعلومات.
                </div>
            @endif
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-list-alt"></i>
                <h3>0</h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-star"></i>
                <h3>0.0</h3>
                <p>Average Rating</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign"></i>
                <h3>0</h3>
                <p>Total Earnings</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>0</h3>
                <p>Customers Served</p>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="actions-grid">
                <a href="#" class="action-btn">
                    <i class="fas fa-user-edit"></i>
                    <span>Edit Profile</span>
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-list-alt"></i>
                    <span>View Orders</span>
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-cog"></i>
                    <span>Manage Services</span>
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-chart-bar"></i>
                    <span>View Reports</span>
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-question-circle"></i>
                    <span>Help & Support</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add any JavaScript functionality here
            console.log('Agent dashboard loaded');
        });
    </script>
</body>
</html>
