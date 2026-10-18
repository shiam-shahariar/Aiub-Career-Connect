const previewButtons = document.querySelectorAll('.device-btn');
const navToggle = document.querySelector('.nav-toggle');
const mainNav = document.querySelector('.main-nav');

if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
        const isOpen = mainNav.classList.toggle('is-open');
        navToggle.setAttribute('aria-expanded', String(isOpen));
    });

    mainNav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            mainNav.classList.remove('is-open');
            navToggle.setAttribute('aria-expanded', 'false');
        });
    });
}

previewButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const device = button.dataset.device;
        document.body.classList.remove('device-preview--desktop', 'device-preview--laptop', 'device-preview--tablet', 'device-preview--mobile', 'device-preview--normal');
        document.body.classList.add(device === 'normal' ? 'device-preview--normal' : `device-preview--${device}`);
        previewButtons.forEach((btn) => btn.classList.toggle('is-active', btn === button));
    });
});

const filterForm = document.getElementById('opportunity-filter-form');
if (filterForm) {
    filterForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        params.set('ajax', '1');
        params.set('page', 'opportunities');

        const resultsContainer = document.getElementById('opportunity-results');
        if (!resultsContainer) return;

        resultsContainer.innerHTML = '<div class="message-panel"><h3>Loading opportunities...</h3></div>';

        try {
            const response = await fetch(`index.php?${params.toString()}`);
            const data = await response.json();

            if (!data.success || !Array.isArray(data.results)) {
                throw new Error('Invalid response');
            }

            if (data.results.length === 0) {
                resultsContainer.innerHTML = '<div class="message-panel"><h3>No opportunities match your filters.</h3><p>Try a broader search or another department.</p></div>';
                return;
            }

            resultsContainer.innerHTML = data.results.map((opportunity) => `
                <article class="listing-card">
                    <div class="listing-card__meta">
                        <span class="badge badge--soft">${opportunity.type}</span>
                        <span>${opportunity.location}</span>
                    </div>
                    <h3>${opportunity.title}</h3>
                    <p class="company-name">${opportunity.company}</p>
                    <p>${opportunity.description}</p>
                    <div class="listing-card__foot">
                        <small>Deadline: ${opportunity.deadline}</small>
                        <a href="index.php?page=opportunity-detail&id=${opportunity.id}" class="btn btn--secondary">View details</a>
                    </div>
                </article>
            `).join('');
        } catch (error) {
            resultsContainer.innerHTML = '<div class="message-panel"><h3>Unable to load opportunities.</h3><p>Please try again later.</p></div>';
        }
    });
}
