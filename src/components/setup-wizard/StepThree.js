import { stepThree } from '../../setupWizardData/setupWizardData';

export const StepThreeHTML = `
 <main class="setup-wizard__body-content">
        <!-- done section container -->
        <section class="setup-wizard__done-section-container">
            <!-- text content -->
            <div class="setup-wizard__done-text-content setup-wizard__done-text-content--done-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="63" height="50" viewBox="0 0 63 50" fill="none">
                    <path d="M28.5173 0C27.7776 0.245902 27.3705 0.819672 26.929 1.89155C28.1821 2.64466 29.258 3.70905 30.0711 5C30.4324 4.23707 30.8911 3.85246 31.929 3.28499C31.0103 1.94265 29.8486 0.824145 28.5173 0Z" fill="url(#paint0_linear_2031_104)"/>
                    <path d="M6.8632 11C8.42842 9.58755 9.5228 8.31517 10 6.69844C9.27904 5.84953 8.26493 5.25063 7.12407 5C6.55542 6.65516 5.46906 8.12054 4 9.214C5.46978 9.52918 6.27784 9.92023 6.8632 11Z" fill="#FF44BC"/>
                    <path d="M4 26.366C3.20341 26.0034 2.52632 25.244 1.633 23C0.866845 23.6494 0.294407 24.5776 0 25.6477C0.668138 27.0311 1.62025 28.1853 2.7653 29C2.92952 27.9847 3.36267 27.0607 4 26.366Z" fill="url(#paint1_linear_2031_104)"/>
                    <path d="M51.2501 10.2419C53.7606 8.99918 55.1516 8.51349 57.515 8.63293C57.3194 7.54072 57.3654 6.8157 57.7709 6.24135C55.2212 5.93799 53.4531 6.33534 50.6749 7.41476C50.615 8.39132 50.8134 9.36643 51.2501 10.2419Z" fill="url(#paint2_linear_2031_104)"/>
                    <path d="M62.1065 24C60.4858 24.7257 58.7533 25.0842 57.0087 25.0548C56.9494 26.0919 57.1949 27.1222 57.7075 27.9876C59.4928 28.0587 61.2774 27.8244 63 27.293C62.0435 26.0388 61.7399 25.2284 62.1065 24Z" fill="#EE8134"/>
                    <circle cx="34" cy="32" r="17.5" stroke="#216DEF"/>
                    <path d="M41.9848 26.3328L30.6515 37.6661L25.5 32.5146" stroke="#216DEF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <defs>
                        <linearGradient id="paint0_linear_2031_104" x1="6162.56" y1="2165.93" x2="6183.4" y2="2144.35" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#FF5C5C"/>
                        <stop offset="0.3" stop-color="#F44444"/>
                        <stop offset="0.93" stop-color="#D60707"/>
                        <stop offset="1" stop-color="#D30000"/>
                        </linearGradient>
                        <linearGradient id="paint1_linear_2031_104" x1="11265.7" y1="8815.03" x2="11241.4" y2="8836.24" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#4D8EFF"/>
                        <stop offset="0.43" stop-color="#3F76FF"/>
                        <stop offset="1" stop-color="#2850FF"/>
                        </linearGradient>
                        <linearGradient id="paint2_linear_2031_104" x1="-8914.56" y1="11377.5" x2="-8898.45" y2="11420.1" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#4D8EFF"/>
                        <stop offset="0.43" stop-color="#3F76FF"/>
                        <stop offset="1" stop-color="#2850FF"/>
                        </linearGradient>
                    </defs>
                </svg>

                <h1 class="setup-wizard__done-heading setup-wizard__heading-one">
                    ${ stepThree?.heading }
                    <span class="setup-wizard__heading-one-highlight">${ stepThree?.strong_heading }</span>
                </h1>

            </div>

            <!-- testimonial container -->
            <div class="setup-wizard__testimonial">
                <h2 class="setup-wizard__testimonial-title">${ stepThree?.testimonials_heading }</h2>
                <div class="setup-wizard__testimonial-card">
                    <div class="setup-wizard__testimonial-single-card">
                        <p class="setup-wizard__testimonial-text-content">
                            ${ stepThree?.testimonials_description[0] }
                        </p>
                        <p class="setup-wizard__testimonial-text-author">
                            - ${ stepThree?.testimonials_author_name[0] }
                        </p>

                        <div class="setup-wizard__testimonial-quote-icon">

                            <svg xmlns="http://www.w3.org/2000/svg" width="113" height="96" viewBox="0 0 113 96" fill="none">
                                <g filter="url(#filter0_dd_192_605)">
                                    <path d="M46.3642 40L39.8188 53.0909H52.9097L52.9097 76H30.0006L30.0006 53.0909L36.5461 40H46.3642ZM75.8188 40L69.2733 53.0909H82.3643L82.3643 76L59.4552 76L59.4552 53.0909L66.0006 40L75.8188 40Z" fill="white"/>
                                </g>
                                <defs>
                                    <filter id="filter0_dd_192_605" x="0.000610352" y="0" width="112.364" height="96" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                    <feOffset dy="-10"/>
                                    <feGaussianBlur stdDeviation="15"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.129412 0 0 0 0 0.427451 0 0 0 0 0.941176 0 0 0 0.12 0"/>
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_192_605"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                    <feOffset dy="1"/>
                                    <feGaussianBlur stdDeviation="0.5"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.129412 0 0 0 0 0.427451 0 0 0 0 0.941176 0 0 0 0.1 0"/>
                                    <feBlend mode="normal" in2="effect1_dropShadow_192_605" result="effect2_dropShadow_192_605"/>
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow_192_605" result="shape"/>
                                    </filter>
                                </defs>
                            </svg>
                        </div>

                    </div>

                    <div class="setup-wizard__testimonial-single-card">
                        <p class="setup-wizard__testimonial-text-content">
                            ${ stepThree?.testimonials_description[1] }
                        </p>

                        <p class="setup-wizard__testimonial-text-author">
                            -  ${ stepThree?.testimonials_author_name[1] }
                        </p>

                        <div class="setup-wizard__testimonial-quote-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="113" height="96" viewBox="0 0 113 96" fill="none">
                                <g filter="url(#filter0_dd_192_605)">
                                    <path d="M46.3642 40L39.8188 53.0909H52.9097L52.9097 76H30.0006L30.0006 53.0909L36.5461 40H46.3642ZM75.8188 40L69.2733 53.0909H82.3643L82.3643 76L59.4552 76L59.4552 53.0909L66.0006 40L75.8188 40Z" fill="white"/>
                                </g>
                                <defs>
                                    <filter id="filter0_dd_192_605" x="0.000610352" y="0" width="112.364" height="96" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                    <feOffset dy="-10"/>
                                    <feGaussianBlur stdDeviation="15"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.129412 0 0 0 0 0.427451 0 0 0 0 0.941176 0 0 0 0.12 0"/>
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_192_605"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                    <feOffset dy="1"/>
                                    <feGaussianBlur stdDeviation="0.5"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.129412 0 0 0 0 0.427451 0 0 0 0 0.941176 0 0 0 0.1 0"/>
                                    <feBlend mode="normal" in2="effect1_dropShadow_192_605" result="effect2_dropShadow_192_605"/>
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow_192_605" result="shape"/>
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- subscribe button -->
            <div class="setup-wizard__subscribe-button-container">
                <!-- switcher -->
                <label class="setup-wizard__switch">
                    <input type="checkbox" id="product-recommendation-toggle-button" checked="checked">
                    <span class="setup-wizard__switch-slider setup-wizard__switch-round"></span>
                </label>
                <p>
                    ${ stepThree?.checkbox_input_text }
                </p>
            </div>
        </section>


        <!-- setup wizard buttons -->
        <section class="setup-wizard__footer-buttons">
            <a class="setup-wizard__button-left product-recommendation-create-contact lets-create-first-recommendation-engine" target="_self">
                ${ stepThree?.button_text[0] }
            </a>
        </section>

    </main>
`;
