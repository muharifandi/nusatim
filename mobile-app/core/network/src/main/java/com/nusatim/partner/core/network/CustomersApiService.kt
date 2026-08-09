package com.nusatim.partner.core.network

import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.CustomerResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import retrofit2.http.*

interface CustomersApiService {

    @GET("customers")
    suspend fun getCustomers(
        @Query("lead_id") leadId: Int? = null,
        @Query("page") page: Int? = null,
        @Query("per_page") perPage: Int? = null
    ): PagedBaseResponse<CustomerResponse>

    @GET("customers/{id}")
    suspend fun getCustomerDetail(@Path("id") id: Int): BaseResponse<CustomerResponse>

    @PUT("customers/{id}")
    suspend fun updateCustomer(
        @Path("id") id: Int,
        @Body request: Map<String, @JvmSuppressWildcards Any?>
    ): BaseResponse<CustomerResponse>

    @PATCH("customers/{id}/progress")
    suspend fun updateCustomerProgress(
        @Path("id") id: Int,
        @Body request: Map<String, Int>
    ): BaseResponse<CustomerResponse>
}
