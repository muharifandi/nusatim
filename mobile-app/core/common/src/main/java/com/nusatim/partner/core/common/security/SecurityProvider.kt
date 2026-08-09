package com.nusatim.partner.core.common.security

import android.content.Context
import android.os.Build
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKey
import com.scottyab.rootbeer.RootBeer
import dagger.hilt.android.qualifiers.ApplicationContext
import java.io.BufferedReader
import java.io.File
import java.io.FileReader
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class SecurityProvider @Inject constructor(
    @ApplicationContext private val context: Context
) {
    private val masterKey = MasterKey.Builder(context)
        .setKeyScheme(MasterKey.KeyScheme.AES256_GCM)
        .build()

    private val encryptedPrefs = EncryptedSharedPreferences.create(
        context,
        "secure_prefs",
        masterKey,
        EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
        EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM
    )

    /**
     * Komprehensif Root Detection
     */
    fun isDeviceRooted(): Boolean {
        return RootBeer(context).isRooted
    }

    /**
     * Anti-Emulator Detection
     * Mempersulit analisis otomatis oleh sandbox malware/penyerang.
     */
    fun isRunningOnEmulator(): Boolean {
        return (Build.BRAND.startsWith("generic") && Build.DEVICE.startsWith("generic"))
                || Build.FINGERPRINT.startsWith("generic")
                || Build.FINGERPRINT.startsWith("unknown")
                || Build.HARDWARE.contains("goldfish")
                || Build.HARDWARE.contains("ranchu")
                || Build.MODEL.contains("google_sdk")
                || Build.MODEL.contains("Emulator")
                || Build.MODEL.contains("Android SDK built for x86")
                || Build.MANUFACTURER.contains("Genymotion")
                || Build.PRODUCT.contains("sdk_google")
                || Build.PRODUCT.contains("google_sdk")
                || Build.PRODUCT.contains("sdk")
                || Build.PRODUCT.contains("sdk_x86")
                || Build.PRODUCT.contains("vbox86p")
                || Build.PRODUCT.contains("emulator")
                || Build.PRODUCT.contains("simulator")
    }

    /**
     * Frida & Hooking Detection
     * Mendeteksi library frida-agent di memory maps.
     */
    fun isHookingDetected(): Boolean {
        try {
            val reader = BufferedReader(FileReader("/proc/self/maps"))
            var line: String?
            while (reader.readLine().also { line = it } != null) {
                if (line?.contains("frida", ignoreCase = true) == true || 
                    line?.contains("xposed", ignoreCase = true) == true) {
                    return true
                }
            }
            reader.close()
        } catch (e: Exception) {
            // Log error
        }
        return false
    }

    /**
     * Tamper Detection: Verify Installer
     * Mencegah sideloading dari app store pihak ketiga yang tidak sah (modded APK).
     */
    fun isAppTampered(): Boolean {
        val validInstallers = listOf("com.android.vending", "com.google.android.feedback")
        val installer = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
            context.packageManager.getInstallSourceInfo(context.packageName).installingPackageName
        } else {
            @Suppress("DEPRECATION")
            context.packageManager.getInstallerPackageName(context.packageName)
        }
        
        // Kembalikan true jika installer tidak ada (sideload) dan bukan dalam mode DEBUG
        return installer == null && !context.applicationInfo.flags.and(1 shl 1).let { it != 0 }
    }

    fun getString(key: String): String? {
        return encryptedPrefs.getString(key, null)
    }

    fun saveString(key: String, value: String) {
        encryptedPrefs.edit().putString(key, value).apply()
    }

    fun getBoolean(key: String, defaultValue: Boolean = false): Boolean {
        return encryptedPrefs.getBoolean(key, defaultValue)
    }

    fun saveBoolean(key: String, value: Boolean) {
        encryptedPrefs.edit().putBoolean(key, value).apply()
    }
}
