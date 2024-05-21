import Swal from 'sweetalert2'

export default {
  methods: {
    async makeDialog(params, feedback_params, confirmation_callback) {
      const default_params = {
        title: 'Voulez-vous confirmer?',
        text: 'Cette action est potentiellement irréversible!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui, je confirme!',
        cancelButtonText: 'Non, annuler.',
      }

      const action = await Swal.fire({ ...default_params, ...(params || {}) })

      if (action.isConfirmed) {
        const default_feedback_params = {
          title: `C'est fait!`,
          text: `L'action a bien été exécutée.`,
          icon: 'success',
        }

        if (typeof confirmation_callback === 'function') {
          await confirmation_callback()
        }

        await Swal.fire({
          ...default_feedback_params,
          ...(feedback_params || {}),
        })
      }
    },
  },
}
