package com.nusatim.partner.core.network

import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.DashboardResponse
import com.nusatim.partner.core.model.dto.LoginResponse
import com.nusatim.partner.core.model.dto.PartnerResponse
import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.http.*

interface AuthApiService {

    @Multipart
    @POST("auth/register")
    suspend fun register(
        @Part("name") name: RequestBody,
        @Part("email") email: RequestBody,
        @Part("password") password: RequestBody,
        @Part("password_confirmation") passwordConfirmation: RequestBody,
        @Part profilePhoto: MultipartBody.Part,
        @Part ktp: MultipartBody.Part,
        @Part npwp: MultipartBody.Part?,
        @Part("bank_name") bankName: RequestBody,
        @Part("bank_account_number") bankAccountNumber: RequestBody,
        @Part("bank_account_holder") bankAccountHolder: RequestBody,
        @Part("agreement_accepted") agreementAccepted: RequestBody,
    ): BaseResponse<PartnerResponse>

    @POST("auth/login")
    suspend fun login(
        @Body request: Map<String, String>
    ): LoginResponse

    @POST("auth/forgot-password")
    suspend fun forgotPassword(
        @Body request: Map<String, String>
    ): Map<String, String>

    @POST("auth/reset-password")
    suspend fun resetPassword(
        @Body request: Map<String, String>
    ): Map<String, String>

    @POST("auth/logout")
    suspend fun logout(): Map<String, String>

    @GET("auth/me")
    suspend fun getCurrentUser(): BaseResponse<PartnerResponse>

    @GET("dashboard")
    suspend fun getDashboard(): DashboardResponse
}
