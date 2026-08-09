package com.nusatim.partner.core.common.network

import com.nusatim.partner.core.common.navigation.NavigationManager
import com.nusatim.partner.core.common.security.SessionManager
import okhttp3.Interceptor
import okhttp3.Response
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class NetworkInterceptor @Inject constructor(
    private val sessionManager: SessionManager,
    private val navigationManager: NavigationManager
) : Interceptor {
    override fun intercept(chain: Interceptor.Chain): Response {
        val originalRequest = chain.request()
        
        val newRequestBuilder = originalRequest.newBuilder()
        
        // Hanya tambah Accept: application/json jika belum ada (misal bukan request gambar dari Coil)
        if (originalRequest.header("Accept") == null) {
            newRequestBuilder.addHeader("Accept", "application/json")
        }
        
        val token = sessionManager.getAuthToken()
        if (!token.isNullOrBlank()) {
            newRequestBuilder.addHeader("Authorization", "Bearer $token")
        }
        
        val response = chain.proceed(newRequestBuilder.build())

        when (response.code) {
            401 -> {
                sessionManager.clearSession()
                navigationManager.navigateTo("partner://login")
            }
            403 -> {
                // Skenario #1 Leads & General: Approval Gate
                navigationManager.navigateTo("partner://approval-status")
            }
        }

        return response
    }
}
