const notificationButton = document.getElementById('notification-button');
const notificationDropdown = document.getElementById('notification-dropdown');

notificationButton.addEventListener('click', () => {
    notificationDropdown.classList.toggle('hidden');
});

const profileButton = document.getElementById('user-profile-button');
const profileDropdown = document.getElementById('user-profile-dropdown');

profileButton.addEventListener('click', () => {
    profileDropdown.classList.toggle('hidden');
});

document.addEventListener('click', (event) => {
    if (!notificationButton.contains(event.target) && !notificationDropdown.contains(event.target)) {
        notificationDropdown.classList.add('hidden');
    }
    
    if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
        profileDropdown.classList.add('hidden');
    }
});

const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('main-content');

// Track sidebar state
let sidebarOpen = true;

sidebarToggle.addEventListener('click', () => {
    sidebarOpen = !sidebarOpen;
    
    if (sidebarOpen) {
        // Open sidebar with animation
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.remove('w-0');
        sidebar.classList.add('w-64');
        sidebar.classList.add('translate-x-0');
        mainContent.classList.add('ml-64');
        mainContent.classList.remove('ml-0');
    } else {
        // Close sidebar with animation
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.remove('w-64');
        sidebar.classList.add('w-0');
        sidebar.classList.add('-translate-x-full');
        mainContent.classList.remove('ml-64');
        mainContent.classList.add('ml-0');
    }
});

// Initialize sidebar state based on screen size
function initializeSidebar() {
    if (window.innerWidth < 768) {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.remove('w-64');
        sidebar.classList.add('w-0');
        mainContent.classList.remove('ml-64');
        mainContent.classList.add('ml-0');
        sidebarOpen = false;
    } else {
        sidebar.classList.add('translate-x-0');
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.remove('w-0');
        sidebar.classList.add('w-64');
        mainContent.classList.add('ml-64');
        mainContent.classList.remove('ml-0');
        sidebarOpen = true;
    }
}

// Run on page load
initializeSidebar();

// Also run when window is resized
window.addEventListener('resize', initializeSidebar);

if (window.innerWidth <= 768) {
    document.addEventListener('click', (event) => {
        if (!sidebarToggle.contains(event.target) &&!sidebar.contains(event.target)) {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-0');
            mainContent.classList.remove('ml-64');
            mainContent.classList.add('ml-0');
            sidebarOpen = false;
        }
    });
}

// Sample data for recent applications
const recentApplications = [
    { id: 1, position: 'Frontend Developer Intern', company: 'Acme Inc.', status: 'Applied', date: '2025-05-01' },
    { id: 2, position: 'Data Analyst Intern', company: 'Beta Corp', status: 'Interview', date: '2025-04-28' },
    { id: 3, position: 'UI/UX Design Intern', company: 'Creative Studios', status: 'Rejected', date: '2025-04-25' },
];

// Function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

// Function to get status color classes
function getStatusClasses(status) {
    switch(status) {
        case 'Applied':
            return 'bg-blue-100 text-blue-700';
        case 'Interview':
            return 'bg-green-100 text-green-700';
        default:
            return 'bg-red-100 text-red-700';
    }
}

// Function to get progress value
function getProgressValue(status) {
    switch(status) {
        case 'Applied':
            return 33;
        case 'Interview':
            return 66;
        default:
            return 100;
    }
}

// Populate recent applications
function populateRecentApplications() {
    const container = document.getElementById('recent-applications-container');
    
    recentApplications.forEach(app => {
        const appElement = document.createElement('div');
        appElement.className = 'space-y-1.5';
        
        const statusClasses = getStatusClasses(app.status);
        const progressValue = getProgressValue(app.status);
        
        appElement.innerHTML = `
            <div class="flex items-center justify-between text-sm">
                <p class="font-medium">${app.position}</p>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium ${statusClasses}">
                    ${app.status}
                </span>
            </div>
            <div class="text-xs text-[var(--muted)]">${app.company}</div>
            <div class="w-full bg-gray-200 rounded-full h-1">
                <div class="bg-[var(--primary)] h-1 rounded-full" style="width: ${progressValue}%"></div>
            </div>
        `;
        container.appendChild(appElement);
    });
}

// Populate recommended internships
function populateRecommendedInternships() {
    const container = document.getElementById('recommended-internships-container');
    
    // Sample data for recommended internships
    const positions = ['Software Engineer Intern', 'Data Science Intern', 'UX Design Intern'];
    const companies = ['TechCorp Inc.', 'DataSystems Ltd.', 'Creative Design Co.'];
    
    for (let i = 0; i < 3; i++) {
        const internshipElement = document.createElement('div');
        internshipElement.className = 'rounded-lg border p-3 transition-all hover:shadow';
        internshipElement.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-building h-5 w-5 text-gray-500 flex items-center justify-center"></i>
                    </div>
                    <div>
                        <p class="font-medium">${positions[i]}</p>
                        <p class="text-sm text-[var(--muted)]">${companies[i]}</p>
                    </div>
                </div>
            </div>
            <div class="mt-2 text-xs">
                <span class="inline-block bg-gray-100 rounded-full px-2 py-1 mr-1">Remote</span>
                <span class="inline-block bg-gray-100 rounded-full px-2 py-1 mr-1">3 months</span>
                <span class="inline-block bg-gray-100 rounded-full px-2 py-1">Paid</span>
            </div>
        `;
        container.appendChild(internshipElement);
    }
}

// Initialize all components when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    populateUpcomingEvents();
    populateRecentApplications();
    populateRecommendedInternships();
});

// Sort Dropdown Logic
const sortButton = document.getElementById('sort-button');
const sortDropdown = document.getElementById('sort-dropdown');
const selectedSort = document.getElementById('selected-sort');

// Toggle dropdown on button click
sortButton.addEventListener('click', function (e) {
    e.stopPropagation();
    sortDropdown.classList.toggle('hidden');
});

// Handle option click
sortDropdown.querySelectorAll('button[data-value]').forEach(btn => {
    btn.addEventListener('click', function () {
        selectedSort.textContent = this.textContent.trim();
        sortDropdown.classList.add('hidden');
    });
});

// Hide dropdown when clicking outside
document.addEventListener('click', function (e) {
    if (!sortDropdown.classList.contains('hidden')) {
        sortDropdown.classList.add('hidden');
    }
});

// Prevent closing when clicking inside dropdown
sortDropdown.addEventListener('click', function (e) {
    e.stopPropagation();
});

