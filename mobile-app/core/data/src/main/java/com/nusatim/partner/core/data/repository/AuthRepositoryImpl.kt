package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.data.repository.BaseRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.AuthApiService
import com.nusatim.partner.core.model.dto.DashboardResponse
import com.nusatim.partner.core.model.dto.LoginResponse
import com.nusatim.partner.core.model.dto.PartnerResponse
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody
import okhttp3.RequestBody
import javax.inject.Inject

class AuthRepositoryImpl @Inject constructor(
    private val apiService: AuthApiService
) : BaseRepository(), AuthRepository {

    override fun register(
        name: RequestBody,
        email: RequestBody,
        password: RequestBody,
        passwordConfirmation: RequestBody,
        profilePhoto: MultipartBody.Part,
        ktp: MultipartBody.Part,
        npwp: MultipartBody.Part?,
        bankName: RequestBody,
        bankAccountNumber: RequestBody,
        bankAccountHolder: RequestBody,
        agreementAccepted: RequestBody
    ): Flow<ResultState<PartnerResponse>> = safeNetworkCall {
        apiService.register(
            name, email, password, passwordConfirmation,
            profilePhoto, ktp, npwp,
            bankName, bankAccountNumber, bankAccountHolder,
            agreementAccepted
        ).data
    }

    override fun login(request: Map<String, String>): Flow<ResultState<LoginResponse>> = safeNetworkCall {
        apiService.login(request)
    }

    override fun forgotPassword(email: String): Flow<ResultState<String>> = safeNetworkCall {
        apiService.forgotPassword(mapOf("email" to email))["message"] ?: "Success"
    }

    override fun resetPassword(request: Map<String, String>): Flow<ResultState<String>> = safeNetworkCall {
        apiService.resetPassword(request)["message"] ?: "Success"
    }

    override fun logout(): Flow<ResultState<String>> = safeNetworkCall {
        apiService.logout()["message"] ?: "Success"
    }

    override fun getCurrentUser(): Flow<ResultState<PartnerResponse>> = safeNetworkCall {
        apiService.getCurrentUser().data
    }

    override fun getDashboard(): Flow<ResultState<DashboardResponse>> = safeNetworkCall {
        apiService.getDashboard()
    }
}
