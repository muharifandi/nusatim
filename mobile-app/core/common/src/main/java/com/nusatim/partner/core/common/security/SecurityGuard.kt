package com.nusatim.partner.core.common.security

import timber.log.Timber
import javax.inject.Inject
import javax.inject.Singleton

/**
 * Created by Foundation Team
 * Orchestrator untuk seluruh pengecekan keamanan runtime.
 * Mengurangi logika di MainActivity untuk maintainability 100%.
 */
@Singleton
class SecurityGuard @Inject constructor(
    private val securityProvider: SecurityProvider
) {
    fun checkIntegrity(isDebug: Boolean, onCompromised: (String) -> Unit) {
        if (securityProvider.isDeviceRooted()) {
            Timber.e("Integrity Check: Rooted device detected")
            // onCompromised("Security Breach: Rooted")
        }

        if (securityProvider.isHookingDetected()) {
            Timber.e("Integrity Check: Hooking framework detected")
            onCompromised("Security Breach: Hooking Tools")
        }

        if (!isDebug && securityProvider.isRunningOnEmulator()) {
            Timber.e("Integrity Check: Emulator detected in production")
            onCompromised("Security Breach: Unauthorized Environment")
        }

        if (!isDebug && securityProvider.isAppTampered()) {
            Timber.e("Integrity Check: App tampering or invalid installer detected")
            onCompromised("Security Breach: App Integrity")
        }
    }
}
