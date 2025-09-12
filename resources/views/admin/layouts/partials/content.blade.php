      <!-- Stats Cards -->
      <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stats-card">
            <div class="stats-icon primary">
              <i class="fas fa-users"></i>
            </div>
            <h3 class="mb-1">1,234</h3>
            <p class="text-muted mb-0">Total Users</p>
            <small class="text-success">
              <i class="fas fa-arrow-up"></i> 12% increase
            </small>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stats-card">
            <div class="stats-icon success">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="mb-1">567</h3>
            <p class="text-muted mb-0">Total Orders</p>
            <small class="text-success">
              <i class="fas fa-arrow-up"></i> 8% increase
            </small>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stats-card">
            <div class="stats-icon warning">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <h3 class="mb-1">$89,123</h3>
            <p class="text-muted mb-0">Total Revenue</p>
            <small class="text-success">
              <i class="fas fa-arrow-up"></i> 15% increase
            </small>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stats-card">
            <div class="stats-icon danger">
              <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="mb-1">78.5%</h3>
            <p class="text-muted mb-0">Conversion Rate</p>
            <small class="text-danger">
              <i class="fas fa-arrow-down"></i> 3% decrease
            </small>
          </div>
        </div>
      </div>

      <!-- Quick Actions & Recent Activity -->
      <div class="row">
        <div class="col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-header">
              <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6 mb-3">
                  <a href="users-add.html" class="quick-action-card">
                    <div class="quick-action-icon primary">
                      <i class="fas fa-user-plus"></i>
                    </div>
                    <h6>Add User</h6>
                  </a>
                </div>
                <div class="col-6 mb-3">
                  <a href="products-add.html" class="quick-action-card">
                    <div class="quick-action-icon success">
                      <i class="fas fa-box"></i>
                    </div>
                    <h6>Add Product</h6>
                  </a>
                </div>
                <div class="col-6 mb-3">
                  <a href="orders.html" class="quick-action-card">
                    <div class="quick-action-icon warning">
                      <i class="fas fa-receipt"></i>
                    </div>
                    <h6>View Orders</h6>
                  </a>
                </div>
                <div class="col-6 mb-3">
                  <a href="analytics-sales.html" class="quick-action-card">
                    <div class="quick-action-icon danger">
                      <i class="fas fa-chart-bar"></i>
                    </div>
                    <h6>Analytics</h6>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-8 mb-4">
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Recent Activities</h5>
              <a href="#" class="text-primary">View All</a>
            </div>
            <div class="card-body">
              <div class="timeline">
                <div class="timeline-item">
                  <div class="timeline-content">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <h6 class="mb-1">New user registration</h6>
                        <p class="mb-1 text-muted">Sarah Johnson joined the platform</p>
                      </div>
                      <span class="timeline-time">2 min ago</span>
                    </div>
                  </div>
                </div>
                <div class="timeline-item">
                  <div class="timeline-content">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <h6 class="mb-1">Order completed</h6>
                        <p class="mb-1 text-muted">Order #1234 has been successfully processed
                        </p>
                      </div>
                      <span class="timeline-time">5 min ago</span>
                    </div>
                  </div>
                </div>
                <div class="timeline-item">
                  <div class="timeline-content">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <h6 class="mb-1">Product updated</h6>
                        <p class="mb-1 text-muted">MacBook Pro inventory updated</p>
                      </div>
                      <span class="timeline-time">10 min ago</span>
                    </div>
                  </div>
                </div>
                <div class="timeline-item">
                  <div class="timeline-content">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <h6 class="mb-1">System backup</h6>
                        <p class="mb-1 text-muted">Daily backup completed successfully</p>
                      </div>
                      <span class="timeline-time">1 hour ago</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders Table -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Recent Orders</h5>
          <a href="orders.html" class="btn btn-primary btn-sm">View All Orders</a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Product</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#1234</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="user-avatar bg-primary mr-2" style="width: 30px; height: 30px; font-size: 12px;">JS
                      </div>
                      John Smith
                    </div>
                  </td>
                  <td>MacBook Pro 16"</td>
                  <td>$2,499</td>
                  <td><span class="badge badge-success">Completed</span></td>
                  <td>2 hours ago</td>
                </tr>
                <tr>
                  <td>#1233</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="user-avatar bg-warning mr-2" style="width: 30px; height: 30px; font-size: 12px;">MJ
                      </div>
                      Mary Johnson
                    </div>
                  </td>
                  <td>iPhone 15 Pro</td>
                  <td>$1,199</td>
                  <td><span class="badge badge-warning">Processing</span></td>
                  <td>4 hours ago</td>
                </tr>
                <tr>
                  <td>#1232</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="user-avatar bg-info mr-2" style="width: 30px; height: 30px; font-size: 12px;">RW
                      </div>
                      Robert Wilson
                    </div>
                  </td>
                  <td>Cotton T-Shirt</td>
                  <td>$29</td>
                  <td><span class="badge badge-danger">Cancelled</span></td>
                  <td>6 hours ago</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
