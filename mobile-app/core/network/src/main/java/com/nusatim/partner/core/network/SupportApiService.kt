package com.nusatim.partner.core.network

import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.SupportTicketResponse
import retrofit2.http.*

interface SupportApiService {

    @GET("support-tickets")
    suspend fun getSupportTickets(
        @Query("page") page: Int? = null,
        @Query("per_page") perPage: Int? = null
    ): PagedBaseResponse<SupportTicketResponse>

    @POST("support-tickets")
    suspend fun createSupportTicket(@Body request: Map<String, String>): BaseResponse<SupportTicketResponse>

    @GET("support-tickets/{id}")
    suspend fun getSupportTicketDetail(@Path("id") id: Int): BaseResponse<SupportTicketResponse>
}
