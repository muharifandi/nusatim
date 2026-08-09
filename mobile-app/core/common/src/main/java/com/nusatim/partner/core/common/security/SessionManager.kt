package com.nusatim.partner.core.common.security

import com.nusatim.partner.core.model.dto.PartnerResponse
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class SessionManager @Inject constructor(
    private val securityProvider: SecurityProvider
) {
    companion object {
        private const val KEY_AUTH_TOKEN = "auth_token"
        private const val KEY_PARTNER_NAME = "partner_name"
        private const val KEY_PARTNER_PHOTO = "partner_photo"
        private const val KEY_PARTNER_STATUS = "partner_status"
        private const val KEY_INTRO_COMPLETED = "intro_completed"
    }

    fun isIntroCompleted(): Boolean {
        return securityProvider.getBoolean(KEY_INTRO_COMPLETED, false)
    }

    fun setIntroCompleted(isCompleted: Boolean) {
        securityProvider.saveBoolean(KEY_INTRO_COMPLETED, isCompleted)
    }

    fun saveAuthToken(token: String) {
        securityProvider.saveString(KEY_AUTH_TOKEN, token)
    }

    fun getAuthToken(): String? {
        return securityProvider.getString(KEY_AUTH_TOKEN)
    }

    fun savePartnerName(name: String) {
        securityProvider.saveString(KEY_PARTNER_NAME, name)
    }

    fun getPartnerName(): String? {
        return securityProvider.getString(KEY_PARTNER_NAME)
    }

    fun savePartnerPhoto(photoUrl: String) {
        securityProvider.saveString(KEY_PARTNER_PHOTO, photoUrl)
    }

    fun getPartnerPhoto(): String? {
        return securityProvider.getString(KEY_PARTNER_PHOTO)
    }

    fun savePartnerStatus(status: String) {
        securityProvider.saveString(KEY_PARTNER_STATUS, status)
    }

    fun getPartnerStatus(): String? {
        return securityProvider.getString(KEY_PARTNER_STATUS)
    }

    fun isUserLoggedIn(): Boolean {
        return getAuthToken() != null
    }

    fun isPartnerApproved(): Boolean {
        return getPartnerStatus() == "approved"
    }

    fun clearSession() {
        securityProvider.saveString(KEY_AUTH_TOKEN, "") // Clear logic
        securityProvider.saveString(KEY_PARTNER_STATUS, "")
    }
}
