package com.nusatim.partner.core.network

import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.LeadActivityResponse
import com.nusatim.partner.core.model.dto.LeadDocumentResponse
import com.nusatim.partner.core.model.dto.LeadReminderResponse
import com.nusatim.partner.core.model.dto.LeadResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.PipelineResponse
import okhttp3.MultipartBody
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.Multipart
import retrofit2.http.PATCH
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Part
import retrofit2.http.Path
import retrofit2.http.Query

interface LeadsApiService {

    @GET("leads")
    suspend fun getLeads(
        @Query("status") status: String? = null,
        @Query("service_id") serviceId: Int? = null,
        @Query("search") search: String? = null,
        @Query("page") page: Int? = null,
        @Query("per_page") perPage: Int? = null
    ): PagedBaseResponse<LeadResponse>

    @POST("leads")
    suspend fun createLead(@Body request: Map<String, @JvmSuppressWildcards Any?>): BaseResponse<LeadResponse>

    @GET("leads/{id}")
    suspend fun getLeadDetail(@Path("id") id: Int): BaseResponse<LeadResponse>

    @PUT("leads/{id}")
    suspend fun updateLead(@Path("id") id: Int, @Body request: Map<String, @JvmSuppressWildcards Any?>): BaseResponse<LeadResponse>

    @PATCH("leads/{id}/status")
    suspend fun updateLeadStatus(@Path("id") id: Int, @Body request: Map<String, String>): BaseResponse<LeadResponse>

    @DELETE("leads/{id}")
    suspend fun deleteLead(@Path("id") id: Int)

    @GET("pipeline")
    suspend fun getPipeline(
        @Query("service_id") serviceId: Int? = null,
        @Query("date_from") dateFrom: String? = null,
        @Query("date_to") dateTo: String? = null
    ): PipelineResponse

    // Reminders
    @GET("leads/{id}/reminders")
    suspend fun getLeadReminders(@Path("id") id: Int): BaseResponse<List<LeadReminderResponse>>

    @POST("leads/{id}/reminders")
    suspend fun createLeadReminder(@Path("id") id: Int, @Body request: Map<String, @JvmSuppressWildcards Any?>): LeadReminderResponse

    @PATCH("leads/{leadId}/reminders/{reminderId}/complete")
    suspend fun completeReminder(@Path("leadId") leadId: Int, @Path("reminderId") reminderId: Int): LeadReminderResponse

    @PUT("leads/{leadId}/reminders/{reminderId}")
    suspend fun updateReminder(
        @Path("leadId") leadId: Int,
        @Path("reminderId") reminderId: Int,
        @Body request: Map<String, @JvmSuppressWildcards Any?>
    ): LeadReminderResponse

    @DELETE("leads/{leadId}/reminders/{reminderId}")
    suspend fun deleteReminder(@Path("leadId") leadId: Int, @Path("reminderId") reminderId: Int)

    // Documents
    @GET("leads/{id}/documents")
    suspend fun getLeadDocuments(@Path("id") id: Int): BaseResponse<List<LeadDocumentResponse>>

    @Multipart
    @POST("leads/{id}/documents")
    suspend fun uploadLeadDocument(
        @Path("id") id: Int,
        @Part file: MultipartBody.Part,
        @Part("original_name") originalName: String? = null
    ): LeadDocumentResponse

    @DELETE("leads/{leadId}/documents/{documentId}")
    suspend fun deleteLeadDocument(@Path("leadId") leadId: Int, @Path("documentId") documentId: Int)

    // Activities
    @GET("leads/{id}/activities")
    suspend fun getLeadActivities(@Path("id") id: Int): BaseResponse<List<LeadActivityResponse>>

    @POST("leads/{id}/activities")
    suspend fun createLeadNote(@Path("id") id: Int, @Body request: Map<String, String>): LeadActivityResponse
}
