import api from './api'

export default {
  noterEvenement(evenementId, payload) {
    return api.post(`/evenements/${evenementId}/avis`, payload)
  },

  noterIntervenant(intervenantId, payload) {
    return api.post(`/intervenants/${intervenantId}/avis`, payload)
  },

  avisEvenement(evenementId) {
    return api.get(`/evenements/${evenementId}/avis`)
  },

  avisIntervenant(intervenantId) {
    return api.get(`/intervenants/${intervenantId}/avis`)
  },

  pageNotation(qrCode) {
    return api.get(`/noter/${qrCode}`)
  },

  noterEvenementParQrCode(qrCode, payload) {
    return api.post(`/noter/${qrCode}/evenement`, payload)
  },

  noterIntervenantParQrCode(qrCode, intervenantId, payload) {
    return api.post(`/noter/${qrCode}/intervenants/${intervenantId}`, payload)
  },
}