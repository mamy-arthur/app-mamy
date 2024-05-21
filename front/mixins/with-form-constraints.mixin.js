export default {
	methods: {
		onlyNumberAndAlphabetic(event) {
	      if ( !(/[a-zA-Z0-9]/).test(event.key) ) {
	        event.preventDefault()
	      }
	    }
	}
}