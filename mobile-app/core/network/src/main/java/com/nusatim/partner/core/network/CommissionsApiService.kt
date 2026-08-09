package com.nusatim.partner.core.network

import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.CommissionResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import retrofit2.http.*

interface CommissionsApiService {

    @GET("commissions")
    suspend fun getCommissions(
        @Query("status") status: String? = null,
        @Query("page") page: Int? = null,
        @Query("per_page") perPage: Int? = null
    ): PagedBaseResponse<CommissionResponse>

    @GET("commissions/{id}")
    suspend fun getCommissionDetail(@Path("id") id: Int): BaseResponse<CommissionResponse>
}
