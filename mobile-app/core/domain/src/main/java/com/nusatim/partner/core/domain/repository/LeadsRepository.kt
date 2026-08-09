package com.nusatim.partner.core.domain.repository

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.*
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody

interface LeadsRepository {
    fun getLeads(
        status: String? = null,
        serviceId: Int? = null,
        search: String? = null,
        page: Int? = null,
        perPage: Int? = null
    ): Flow<ResultState<PagedBaseResponse<LeadResponse>>>

    fun createLead(request: Map<String, Any?>): Flow<ResultState<LeadResponse>>
    
    fun getLeadDetail(id: Int): Flow<ResultState<LeadResponse>>
    
    fun updateLead(id: Int, request: Map<String, Any?>): Flow<ResultState<LeadResponse>>
    
    fun updateLeadStatus(id: Int, status: String): Flow<ResultState<LeadResponse>>
    
    fun deleteLead(id: Int): Flow<ResultState<Unit>>

    fun getPipeline(
        serviceId: Int? = null,
        dateFrom: String? = null,
        dateTo: String? = null
    ): Flow<ResultState<PipelineResponse>>

    // Reminders
    fun getLeadReminders(id: Int): Flow<ResultState<List<LeadReminderResponse>>>
    
    fun createLeadReminder(id: Int, request: Map<String, Any?>): Flow<ResultState<LeadReminderResponse>>
    
    fun completeReminder(leadId: Int, reminderId: Int): Flow<ResultState<LeadReminderResponse>>
    
    fun updateReminder(leadId: Int, reminderId: Int, request: Map<String, Any?>): Flow<ResultState<LeadReminderResponse>>
    
    fun deleteReminder(leadId: Int, reminderId: Int): Flow<ResultState<Unit>>

    // Documents
    fun getLeadDocuments(id: Int): Flow<ResultState<List<LeadDocumentResponse>>>
    
    fun uploadLeadDocument(id: Int, file: MultipartBody.Part, originalName: String? = null): Flow<ResultState<LeadDocumentResponse>>

    fun deleteLeadDocument(leadId: Int, documentId: Int): Flow<ResultState<Unit>>

    // Activities
    fun getLeadActivities(id: Int): Flow<ResultState<List<LeadActivityResponse>>>
    
    fun createLeadNote(id: Int, note: String): Flow<ResultState<LeadActivityResponse>>
}
