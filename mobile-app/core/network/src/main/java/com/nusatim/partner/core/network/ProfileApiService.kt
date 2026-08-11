package com.nusatim.partner.core.network

import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.PartnerResponse
import okhttp3.MultipartBody
import retrofit2.http.*

interface ProfileApiService {

    @GET("profile")
    suspend fun getProfile(): BaseResponse<PartnerResponse>

    @PUT("profile")
    suspend fun updateProfile(
        @Body request: Map<String, @JvmSuppressWildcards Any>
    ): BaseResponse<PartnerResponse>

    @Multipart
    @POST("profile/photo")
    suspend fun updatePhoto(
        @Part profilePhoto: MultipartBody.Part
    ): BaseResponse<PartnerResponse>

    @Multipart
    @POST("profile/ktp")
    suspend fun updateKtp(
        @Part ktp: MultipartBody.Part
    ): BaseResponse<PartnerResponse>

    @Multipart
    @POST("profile/npwp")
    suspend fun updateNpwp(
        @Part npwp: MultipartBody.Part
    ): BaseResponse<PartnerResponse>

    @PUT("profile/password")
    suspend fun updatePassword(
        @Body request: Map<String, String>
    ): Map<String, String>

    @DELETE("profile")
    suspend fun deleteAccount(): Map<String, String>
}
