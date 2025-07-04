const featuredInternships = [
    {
        id: "1",
        title: "Frontend Developer Intern",
        company: {
            name: "TechCorp",
            logo: "https://images.unsplash.com/photo-1549923746-c502d488b3ea?w=800&auto=format&fit=crop&q=60",
        },
        location: "San Francisco, CA",
        duration: "3 months", 
        description: "Work with modern web technologies on exciting projects.",
    },
    {
        id: "2",
        title: "Data Science Intern",
        company: {
            name: "DataViz Inc.",
            logo: "https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=800&auto=format&fit=crop&q=60",
        },
        location: "Boston, MA",
        duration: "6 months",
        description: "Analyze datasets and build machine learning models for real clients.",
    },
    {
        id: "3",
        title: "UX/UI Design Intern",
        company: {
            name: "Creative Solutions",
            logo: "https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=800&auto=format&fit=crop&q=60",
        },
        location: "Remote",
        duration: "4 months",
        description:
        "Design beautiful and intuitive interfaces for web/mobile platforms.",
    },
];

const container = document.getElementById("featured-internships");

featuredInternships.forEach((intern) => {
    const div = document.createElement("div");
    div.className =
        "bg-white border rounded-xl shadow-sm p-4 flex flex-col gap-2 hover:shadow-md transition";

    div.innerHTML = `
        <div class="flex items-center gap-3 mb-2">
            <img src="${intern.company.logo}" alt="${intern.company.name}"
            class="w-10 h-10 rounded-md object-cover border" />
            <div>
                <h3 class="font-medium text-lg">${intern.title}</h3>
                <span class="text-xs text-[var(--muted)]">${intern.company.name}</span>
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm text-[var(--muted)]">
            <span>${intern.location}</span>
            <span>&bull;</span>
            <span>${intern.duration}</span>
        </div>
        <p class="text-sm mt-2 mb-3 min-h-[38px]">${intern.description}</p>
        <a href="/internships/${intern.id}" class="mt-auto text-[var(--primary)] font-medium text-sm inline-flex items-center gap-1 hover:underline">
            View Details
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
        `;
    container.appendChild(div);
});
