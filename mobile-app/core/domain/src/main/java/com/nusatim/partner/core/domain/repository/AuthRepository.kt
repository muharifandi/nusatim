package com.nusatim.partner.core.domain.repository

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.DashboardResponse
import com.nusatim.partner.core.model.dto.LoginResponse
import com.nusatim.partner.core.model.dto.PartnerResponse
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody
import okhttp3.RequestBody

interface AuthRepository {
    fun register(
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
    ): Flow<ResultState<PartnerResponse>>

    fun login(request: Map<String, String>): Flow<ResultState<LoginResponse>>

    fun forgotPassword(email: String): Flow<ResultState<String>>

    fun resetPassword(request: Map<String, String>): Flow<ResultState<String>>
    
    fun logout(): Flow<ResultState<String>>
    
    fun getCurrentUser(): Flow<ResultState<PartnerResponse>>

    fun getDashboard(): Flow<ResultState<DashboardResponse>>
}
