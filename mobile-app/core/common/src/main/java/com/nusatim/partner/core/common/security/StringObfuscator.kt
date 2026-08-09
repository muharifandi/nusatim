package com.nusatim.partner.core.common.security

import android.util.Base64

/**
 * Created by Foundation Team
 * Utilitas untuk menyembunyikan string sensitif (seperti API Key) dari static analysis.
 * Gunakan [obfuscate] saat development untuk mendapatkan string terenkripsi, 
 * lalu gunakan [deobfuscate] saat runtime.
 */
object StringObfuscator {
    // Kunci enkripsi sederhana (XOR). Ganti secara berkala.
    private const val KEY = "super_secret_obfuscation_key"

    /**
     * Mengubah teks biasa menjadi teks ter-obfuscate (Base64 + XOR).
     */
    fun obfuscate(input: String): String {
        val xor = input.mapIndexed { index, char ->
            (char.code xor KEY[index % KEY.length].code).toChar()
        }.joinToString("")
        return Base64.encodeToString(xor.toByteArray(), Base64.DEFAULT)
    }

    /**
     * Mengembalikan teks asli dari teks ter-obfuscate.
     */
    fun deobfuscate(input: String): String {
        return try {
            val decoded = String(Base64.decode(input, Base64.DEFAULT))
            decoded.mapIndexed { index, char ->
                (char.code xor KEY[index % KEY.length].code).toChar()
            }.joinToString("")
        } catch (e: Exception) {
            "" // Kembalikan string kosong jika gagal (keamanan preventif)
        }
    }
}
