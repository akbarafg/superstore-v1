import { ref, watch } from 'vue';

/**
 * A composable to manage theme and layout direction.
 */
export function useThemeManager() {

    // Initializes a reactive reference for the theme, defaulting to 'light' if no value is found in localStorage
    const theme = ref(localStorage.getItem('theme') || 'light');
    const direction = ref(localStorage.getItem('direction') || 'ltr');


    // Persist changes to localStorage and update the DOM

    watch(theme, (newTheme) => {
        // Store new theme in local storage
        localStorage.setItem('theme', newTheme);
        
        // Update body attribute for styling
        document.body.setAttribute('data-theme', newTheme);
    
        // Determine and apply appropriate color scheme
        const storedTheme = localStorage.getItem('theme');
        
        if (storedTheme === 'auto') {
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-color-theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-color-theme');
            }
        } else if (storedTheme === 'dark') {
            document.documentElement.setAttribute('data-color-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-color-theme');
        }
    });
     
    
    // Methods to update state
    const setTheme = (newTheme) => {
        theme.value = newTheme;
    };

    return {
        theme,
        setTheme,
    };
}





