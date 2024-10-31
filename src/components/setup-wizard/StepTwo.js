import { wooCommerceIcon, stepTwo } from '../../setupWizardData/setupWizardData';

export const StepTwoHTML = `
    <main class="setup-wizard__body-content">
        <section class="setup-wizard__welcome-section-container">
            <h2 class="setup-wizard__feature-heading setup-wizard__heading-one">
                ${ stepTwo?.heading[0] }
                <span class="setup-wizard__heading-one-highlight">${ stepTwo?.strong_heading[0] }</span>
            </h2>

            <div class="rex-wpfm-setup-wizard-essential-plugin">

                <div class="rex-wpfm-setup-plugins-wrapper">

                    <div class="rex-wpfm-setup-single-plugin" data-plugin="woocommerce">

                        <div class="setup-wizard__checkbox-container">
                            <input type="checkbox" id="woocommerce" checked="" disabled="">
                            <label for="woocommerce"></label>
                        </div>

                        <div class="rex-wpfm-setup-plugin-logo">
                            <figure>
                                <img src=${ wooCommerceIcon } alt=${ stepTwo?.img_alt }/>
                            </figure>
                        </div>

                        <div class="rex-wpfm-setup-plugin-info">
                            <h3 class="rex-wpfm-setup-plugin-heading">${ stepTwo?.card_heading }</h3>
                            <p class="rex-wpfm-setup-plugin-subheading">${ stepTwo?.card_text }</p>
                        </div>

                        <span class="rex-wpfm-required-tag">${ stepTwo?.required}</span>

                    </div>

                </div>

            </div>
        </section>
        <!-- welcome-section-container -->


        <!-- setup wizard buttons -->
        <section class="setup-wizard__footer-buttons">
            <a class="setup-wizard__button-left lets-create-first-recommendation-engine">
                ${ stepTwo?.button_text[0] }
            </a>
            <button onClick="nextToggle()" class="setup-wizard__button-right next-step-button">  ${ stepTwo?.button_text[1]} </button>
        </section>
    </main>

`;
