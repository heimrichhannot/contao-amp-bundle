# Configuration

The complete bundle configuration:

```yaml
huh_amp:
    templates:

        # Prototype
        name:

            # Required amp components for this template.
            components:           []

            # Use the original template instead of an _amp substitute.
            amp_template:         false

            # Convert the html code to amp code. Requires lullabot/amp library.
            convert_html:         false
    components:

        # Prototype
        name:
            url:                  ~
```

Example: 

```yaml
# src/Ressources/config/config.yml
huh_amp:
  # Add support for additional templates
  templates:
    my_custom_template:
      components: ['accordion','youtube'] # amp components needed for this template
      amp_template: false # set to true, if the template is already prepared for amp (don't add a _amp suffix)
    # Examples:
    ce_youtube:
      components: ['youtube']
    mod_ampnavigation:
      components: ['sidebar','accordion']
      amp_template: true
    cookiebar: ~
  # Add support for additional amp components
  components:
    accordion: { url: "https://cdn.ampproject.org/v0/amp-accordion-0.1.js" }
    sidebar:   { url: "https://cdn.ampproject.org/v0/amp-sidebar-0.1.js" }
    youtube:   { url: "https://cdn.ampproject.org/v0/amp-youtube-0.1.js" }
```