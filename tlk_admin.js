window.onload = () => {
  const tlkForm = document.getElementById( 'tlk_settings' )

  tlkForm.addEventListener( 'change', (e) => {
    switch ( e.target.name ) {
      case 'auto_display':
        const enabledEls = document.querySelectorAll('.tlk_enabled')
        for (const element of enabledEls) {
          element.checked = e.target.checked
        }
        break;
      case 'tlk_twitter':
      case 'tlk_linkedin':
      case 'tlk_kindle':
        const el = document.getElementById('auto_display')
        if (el.checked) {
          el.checked = false
        }
    }
  } )

  // tlkForm.addEventListener( 'blur' )
}