// Configuration
const API_BASE = '/api';
let currentUser = null;

// Initialize app
document.addEventListener('DOMContentLoaded', () => {
    checkAuth();
    setupEventListeners();
});

// Check authentication status
async function checkAuth() {
    try {
        const response = await fetch(`${API_BASE}/current-user`);
        const data = await response.json();
        
        if (data.success) {
            currentUser = data.user;
            showDashboard();
            loadDashboardData();
        } else {
            showLogin();
        }
    } catch (error) {
        console.error('Auth check failed:', error);
        showLogin();
    }
}

// Setup event listeners
function setupEventListeners() {
    // Login form
    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    
    // Logout
    document.getElementById('btnLogout').addEventListener('click', handleLogout);
    
    // Navigation
    document.querySelectorAll('.nav-item[data-view]').forEach(item => {
        item.addEventListener('click', (e) => {
            const view = e.currentTarget.getAttribute('data-view');
            switchView(view);
        });
    });
    
    // New leave request buttons
    document.getElementById('btnNewLeave').addEventListener('click', () => openLeaveModal());
    document.getElementById('btnNewLeave2').addEventListener('click', () => openLeaveModal());
    
    // Modal controls
    document.getElementById('closeModal').addEventListener('click', closeLeaveModal);
    document.getElementById('cancelModal').addEventListener('click', closeLeaveModal);
    document.getElementById('closeActionModal').addEventListener('click', closeActionModal);
    
    // Leave request form
    document.getElementById('leaveRequestForm').addEventListener('submit', handleLeaveRequest);
    
    // Action modal buttons
    document.getElementById('btnApprove').addEventListener('click', () => {
        document.getElementById('actionType').value = 'approved';
        document.getElementById('rejectionReasonGroup').style.display = 'none';
    });
    
    document.getElementById('btnReject').addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('actionType').value = 'rejected';
        document.getElementById('rejectionReasonGroup').style.display = 'block';
    });
    
    document.getElementById('actionForm').addEventListener('submit', handleActionSubmit);
    
    // Filter status
    const filterStatus = document.getElementById('filterStatus');
    if (filterStatus) {
        filterStatus.addEventListener('change', () => loadAllRequests());
    }
}

// Show/hide views
function showLogin() {
    document.getElementById('loginView').classList.add('active');
    document.getElementById('dashboardView').classList.remove('active');
}

function showDashboard() {
    document.getElementById('loginView').classList.remove('active');
    document.getElementById('dashboardView').classList.add('active');
    
    // Update user info
    document.getElementById('userName').textContent = currentUser.full_name;
    document.getElementById('userRole').textContent = currentUser.role;
    
    // Show/hide menu items based on role
    if (['hr', 'admin', 'manager'].includes(currentUser.role)) {
        document.getElementById('navRequests').style.display = 'flex';
    }
    
    if (['hr', 'admin'].includes(currentUser.role)) {
        document.getElementById('navReports').style.display = 'flex';
    }
}

// Switch between views
function switchView(view) {
    // Update nav items
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-view="${view}"]`).classList.add('active');
    
    // Update content sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    
    switch(view) {
        case 'overview':
            document.getElementById('overviewSection').style.display = 'block';
            loadDashboardData();
            break;
        case 'my-leaves':
            document.getElementById('myLeavesSection').style.display = 'block';
            loadMyLeaves();
            break;
        case 'requests':
            document.getElementById('requestsSection').style.display = 'block';
            loadAllRequests();
            break;
        case 'reports':
            document.getElementById('reportsSection').style.display = 'block';
            loadReports();
            break;
    }
}

// Handle login
async function handleLogin(e) {
    e.preventDefault();
    
    const formData = {
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
    };
    
    try {
        const response = await fetch(`${API_BASE}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentUser = data.user;
            showDashboard();
            loadDashboardData();
        } else {
            showAlert('loginAlert', data.message, 'error');
        }
    } catch (error) {
        showAlert('loginAlert', 'Login failed. Please try again.', 'error');
    }
}

// Handle logout
async function handleLogout() {
    try {
        await fetch(`${API_BASE}/logout`, { method: 'POST' });
        currentUser = null;
        showLogin();
    } catch (error) {
        console.error('Logout failed:', error);
    }
}

// Load dashboard data
async function loadDashboardData() {
    loadLeaveBalance();
    loadRecentRequests();
    loadStatistics();
}

// Load leave balance
async function loadLeaveBalance() {
    try {
        const response = await fetch(`${API_BASE}/leave-balance?user_id=${currentUser.id}`);
        const data = await response.json();
        
        if (data.success) {
            displayLeaveBalance(data.balance);
        }
    } catch (error) {
        console.error('Failed to load leave balance:', error);
    }
}

// Display leave balance
function displayLeaveBalance(balance) {
    const container = document.getElementById('leaveBalanceContent');
    
    if (balance.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-muted);">No leave balance data available.</p>';
        return;
    }
    
    let html = '<div class="table-container"><table><thead><tr>';
    html += '<th>Leave Type</th>';
    html += '<th>Total Days</th>';
    html += '<th>Used Days</th>';
    html += '<th>Remaining</th>';
    html += '<th>Progress</th>';
    html += '</tr></thead><tbody>';
    
    balance.forEach(item => {
        const percentage = (item.used_days / item.total_days) * 100;
        html += `<tr>
            <td><strong>${item.leave_type_name}</strong></td>
            <td>${item.total_days}</td>
            <td>${item.used_days}</td>
            <td><strong style="color: var(--accent-light);">${item.remaining_days}</strong></td>
            <td>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${percentage}%"></div>
                </div>
            </td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// Load recent requests
async function loadRecentRequests() {
    try {
        const response = await fetch(`${API_BASE}/leave-requests?user_id=${currentUser.id}`);
        const data = await response.json();
        
        if (data.success) {
            displayRecentRequests(data.requests.slice(0, 5));
        }
    } catch (error) {
        console.error('Failed to load requests:', error);
    }
}

// Display recent requests
function displayRecentRequests(requests) {
    const container = document.getElementById('recentRequestsContent');
    
    if (requests.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">No leave requests yet.</p>';
        return;
    }
    
    let html = '<div class="table-container"><table><thead><tr>';
    html += '<th>Leave Type</th>';
    html += '<th>Dates</th>';
    html += '<th>Days</th>';
    html += '<th>Status</th>';
    html += '<th>Applied On</th>';
    html += '</tr></thead><tbody>';
    
    requests.forEach(request => {
        const statusBadge = getStatusBadge(request.status);
        const startDate = new Date(request.start_date).toLocaleDateString();
        const endDate = new Date(request.end_date).toLocaleDateString();
        const appliedDate = new Date(request.created_at).toLocaleDateString();
        
        html += `<tr>
            <td><strong>${request.leave_type_name}</strong></td>
            <td>${startDate} - ${endDate}</td>
            <td>${request.total_days} days</td>
            <td>${statusBadge}</td>
            <td>${appliedDate}</td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// Load statistics
async function loadStatistics() {
    try {
        const response = await fetch(`${API_BASE}/leave-statistics?user_id=${currentUser.id}`);
        const data = await response.json();
        
        if (data.success) {
            displayStatistics(data.statistics);
        }
    } catch (error) {
        console.error('Failed to load statistics:', error);
    }
}

// Display statistics
function displayStatistics(stats) {
    const container = document.getElementById('statsGrid');
    
    const html = `
        <div class="stat-card">
            <div class="stat-label">Total Requests</div>
            <div class="stat-value">${stats.total_requests || 0}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value">${stats.pending_requests || 0}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Approved</div>
            <div class="stat-value">${stats.approved_requests || 0}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Days Taken</div>
            <div class="stat-value">${stats.total_days_taken || 0}</div>
        </div>
    `;
    
    container.innerHTML = html;
}

// Load my leaves
async function loadMyLeaves() {
    const container = document.getElementById('myLeavesContent');
    container.innerHTML = '<div class="loading active"><div class="spinner"></div></div>';
    
    try {
        const response = await fetch(`${API_BASE}/leave-requests?user_id=${currentUser.id}`);
        const data = await response.json();
        
        if (data.success) {
            displayMyLeaves(data.requests);
        }
    } catch (error) {
        container.innerHTML = '<p style="text-align: center; color: var(--danger);">Failed to load leave requests.</p>';
    }
}

// Display my leaves
function displayMyLeaves(requests) {
    const container = document.getElementById('myLeavesContent');
    
    if (requests.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">No leave requests found.</p>';
        return;
    }
    
    let html = '<div class="table-container"><table><thead><tr>';
    html += '<th>Leave Type</th>';
    html += '<th>Start Date</th>';
    html += '<th>End Date</th>';
    html += '<th>Days</th>';
    html += '<th>Reason</th>';
    html += '<th>Status</th>';
    html += '<th>Applied On</th>';
    html += '</tr></thead><tbody>';
    
    requests.forEach(request => {
        const statusBadge = getStatusBadge(request.status);
        const startDate = new Date(request.start_date).toLocaleDateString();
        const endDate = new Date(request.end_date).toLocaleDateString();
        const appliedDate = new Date(request.created_at).toLocaleDateString();
        
        html += `<tr>
            <td><strong>${request.leave_type_name}</strong></td>
            <td>${startDate}</td>
            <td>${endDate}</td>
            <td>${request.total_days}</td>
            <td>${request.reason || '-'}</td>
            <td>${statusBadge}</td>
            <td>${appliedDate}</td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// Load all requests (HR/Manager view)
async function loadAllRequests() {
    const container = document.getElementById('allRequestsContent');
    container.innerHTML = '<div class="loading active"><div class="spinner"></div></div>';
    
    const status = document.getElementById('filterStatus').value;
    
    try {
        let url = `${API_BASE}/leave-requests`;
        if (status) url += `?status=${status}`;
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            displayAllRequests(data.requests);
        }
    } catch (error) {
        container.innerHTML = '<p style="text-align: center; color: var(--danger);">Failed to load requests.</p>';
    }
}

// Display all requests
function displayAllRequests(requests) {
    const container = document.getElementById('allRequestsContent');
    
    if (requests.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">No leave requests found.</p>';
        return;
    }
    
    let html = '<div class="table-container"><table><thead><tr>';
    html += '<th>Employee</th>';
    html += '<th>Department</th>';
    html += '<th>Leave Type</th>';
    html += '<th>Dates</th>';
    html += '<th>Days</th>';
    html += '<th>Status</th>';
    html += '<th>Actions</th>';
    html += '</tr></thead><tbody>';
    
    requests.forEach(request => {
        const statusBadge = getStatusBadge(request.status);
        const startDate = new Date(request.start_date).toLocaleDateString();
        const endDate = new Date(request.end_date).toLocaleDateString();
        const employeeName = `${request.first_name} ${request.last_name}`;
        
        let actions = '';
        if (request.status === 'pending') {
            actions = `<button class="btn btn-sm btn-primary" onclick="openActionModal(${request.id})">Review</button>`;
        } else {
            actions = '<span style="color: var(--text-muted);">-</span>';
        }
        
        html += `<tr>
            <td><strong>${employeeName}</strong><br><small style="color: var(--text-muted);">${request.employee_id}</small></td>
            <td>${request.department_name || '-'}</td>
            <td>${request.leave_type_name}</td>
            <td>${startDate} - ${endDate}</td>
            <td>${request.total_days}</td>
            <td>${statusBadge}</td>
            <td>${actions}</td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// Load reports
async function loadReports() {
    const container = document.getElementById('reportsContent');
    container.innerHTML = '<div class="loading active"><div class="spinner"></div></div>';
    
    try {
        const response = await fetch(`${API_BASE}/report-company`);
        const data = await response.json();
        
        if (data.success) {
            displayCompanyReport(data.report);
        }
    } catch (error) {
        container.innerHTML = '<p style="text-align: center; color: var(--danger);">Failed to load reports.</p>';
    }
}

// Display company report
function displayCompanyReport(report) {
    const container = document.getElementById('reportsContent');
    
    let html = `
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Employees</div>
                <div class="stat-value">${report.total_employees}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Requests</div>
                <div class="stat-value">${report.overall_stats.total_requests || 0}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Days Taken</div>
                <div class="stat-value">${report.overall_stats.total_days_taken || 0}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending Approvals</div>
                <div class="stat-value">${report.overall_stats.pending_requests || 0}</div>
            </div>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Leave by Department</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Employees</th>
                            <th>Total Requests</th>
                            <th>Days Taken</th>
                        </tr>
                    </thead>
                    <tbody>
    `;
    
    report.department_breakdown.forEach(dept => {
        html += `<tr>
            <td><strong>${dept.department_name}</strong></td>
            <td>${dept.employee_count}</td>
            <td>${dept.total_requests || 0}</td>
            <td>${dept.days_taken || 0}</td>
        </tr>`;
    });
    
    html += `
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Leave by Type</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Requests</th>
                            <th>Total Days</th>
                        </tr>
                    </thead>
                    <tbody>
    `;
    
    report.type_breakdown.forEach(type => {
        html += `<tr>
            <td><strong>${type.leave_type}</strong></td>
            <td>${type.request_count}</td>
            <td>${type.total_days || 0}</td>
        </tr>`;
    });
    
    html += `
                    </tbody>
                </table>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

// Open leave modal
async function openLeaveModal() {
    document.getElementById('leaveModal').classList.add('active');
    document.getElementById('leaveRequestForm').reset();
    document.getElementById('modalAlert').innerHTML = '';
    
    // Load leave types
    try {
        const response = await fetch(`${API_BASE}/leave-types`);
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('leaveType');
            select.innerHTML = '<option value="">Select leave type...</option>';
            
            data.leave_types.forEach(type => {
                const option = document.createElement('option');
                option.value = type.id;
                option.textContent = type.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Failed to load leave types:', error);
    }
    
    // Set min date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('startDate').min = today;
    document.getElementById('endDate').min = today;
}

// Close leave modal
function closeLeaveModal() {
    document.getElementById('leaveModal').classList.remove('active');
}

// Handle leave request submission
async function handleLeaveRequest(e) {
    e.preventDefault();
    
    const formData = {
        leave_type_id: document.getElementById('leaveType').value,
        start_date: document.getElementById('startDate').value,
        end_date: document.getElementById('endDate').value,
        reason: document.getElementById('reason').value
    };
    
    try {
        const response = await fetch(`${API_BASE}/leave-request`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('modalAlert', `Leave request submitted successfully! Total days: ${data.total_days}`, 'success');
            setTimeout(() => {
                closeLeaveModal();
                loadDashboardData();
            }, 2000);
        } else {
            showAlert('modalAlert', data.message, 'error');
        }
    } catch (error) {
        showAlert('modalAlert', 'Failed to submit request. Please try again.', 'error');
    }
}

// Open action modal
async function openActionModal(requestId) {
    try {
        const response = await fetch(`${API_BASE}/leave-requests?user_id=${currentUser.id}`);
        const data = await response.json();
        
        if (data.success) {
            const allResponse = await fetch(`${API_BASE}/leave-requests`);
            const allData = await allResponse.json();
            
            const request = allData.requests.find(r => r.id === requestId);
            
            if (request) {
                document.getElementById('actionRequestId').value = requestId;
                document.getElementById('actionType').value = 'approved';
                document.getElementById('rejectionReasonGroup').style.display = 'none';
                document.getElementById('rejectionReason').value = '';
                
                const startDate = new Date(request.start_date).toLocaleDateString();
                const endDate = new Date(request.end_date).toLocaleDateString();
                
                const modalBody = `
                    <div style="margin-bottom: 1.5rem;">
                        <p><strong>Employee:</strong> ${request.first_name} ${request.last_name} (${request.employee_id})</p>
                        <p><strong>Department:</strong> ${request.department_name || '-'}</p>
                        <p><strong>Leave Type:</strong> ${request.leave_type_name}</p>
                        <p><strong>Dates:</strong> ${startDate} to ${endDate}</p>
                        <p><strong>Total Days:</strong> ${request.total_days} days</p>
                        <p><strong>Reason:</strong> ${request.reason || 'No reason provided'}</p>
                    </div>
                `;
                
                document.getElementById('actionModalBody').innerHTML = modalBody;
                document.getElementById('actionModal').classList.add('active');
            }
        }
    } catch (error) {
        console.error('Failed to load request details:', error);
    }
}

// Close action modal
function closeActionModal() {
    document.getElementById('actionModal').classList.remove('active');
}

// Handle action submission
async function handleActionSubmit(e) {
    e.preventDefault();
    
    const requestId = document.getElementById('actionRequestId').value;
    const status = document.getElementById('actionType').value;
    const rejectionReason = status === 'rejected' ? document.getElementById('rejectionReason').value : null;
    
    if (status === 'rejected' && !rejectionReason) {
        showAlert('actionModalAlert', 'Please provide a rejection reason.', 'error');
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/leave-request-status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                request_id: requestId,
                status: status,
                rejection_reason: rejectionReason
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('actionModalAlert', `Request ${status} successfully!`, 'success');
            setTimeout(() => {
                closeActionModal();
                loadAllRequests();
            }, 1500);
        } else {
            showAlert('actionModalAlert', data.message, 'error');
        }
    } catch (error) {
        showAlert('actionModalAlert', 'Failed to process request. Please try again.', 'error');
    }
}

// Helper functions
function getStatusBadge(status) {
    const badges = {
        pending: '<span class="badge badge-pending">Pending</span>',
        approved: '<span class="badge badge-approved">Approved</span>',
        rejected: '<span class="badge badge-rejected">Rejected</span>',
        cancelled: '<span class="badge badge-rejected">Cancelled</span>'
    };
    return badges[status] || status;
}

function showAlert(containerId, message, type) {
    const container = document.getElementById(containerId);
    const className = type === 'error' ? 'alert-error' : 'alert-success';
    container.innerHTML = `<div class="alert ${className}">${message}</div>`;
}

// Make openActionModal globally accessible
window.openActionModal = openActionModal;
