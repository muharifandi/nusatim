package com.nusatim.partner.core.network

import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.WithdrawalBalanceResponse
import com.nusatim.partner.core.model.dto.WithdrawalResponse
import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.http.GET
import retrofit2.http.Multipart
import retrofit2.http.POST
import retrofit2.http.Part
import retrofit2.http.Path
import retrofit2.http.Query

interface WithdrawalsApiService {

    @GET("withdrawals/balance")
    suspend fun getBalance(): WithdrawalBalanceResponse

    @GET("withdrawals")
    suspend fun getWithdrawals(
        @Query("page") page: Int? = null,
        @Query("per_page") perPage: Int? = null
    ): PagedBaseResponse<WithdrawalResponse>

    @GET("withdrawals/{id}")
    suspend fun getWithdrawalDetail(@Path("id") id: Int): BaseResponse<WithdrawalResponse>

    @Multipart
    @POST("withdrawals")
    suspend fun requestWithdrawal(
        @Part("amount") amount: RequestBody,
        @Part ktp: MultipartBody.Part,
        @Part("note") note: RequestBody? = null
    ): BaseResponse<WithdrawalResponse>
}
