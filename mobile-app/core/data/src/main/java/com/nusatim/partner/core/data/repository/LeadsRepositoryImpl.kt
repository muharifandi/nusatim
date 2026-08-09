package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.data.repository.BaseRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.LeadsApiService
import com.nusatim.partner.core.model.dto.*
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody
import javax.inject.Inject

class LeadsRepositoryImpl @Inject constructor(
    private val apiService: LeadsApiService
) : BaseRepository(), LeadsRepository {

    override fun getLeads(
        status: String?,
        serviceId: Int?,
        search: String?,
        page: Int?,
        perPage: Int?
    ): Flow<ResultState<PagedBaseResponse<LeadResponse>>> = safeNetworkCall {
        apiService.getLeads(status, serviceId, search, page, perPage)
    }

    override fun createLead(request: Map<String, Any?>): Flow<ResultState<LeadResponse>> = safeNetworkCall {
        apiService.createLead(request).data
    }

    override fun getLeadDetail(id: Int): Flow<ResultState<LeadResponse>> = safeNetworkCall {
        apiService.getLeadDetail(id).data
    }

    override fun updateLead(id: Int, request: Map<String, Any?>): Flow<ResultState<LeadResponse>> = safeNetworkCall {
        apiService.updateLead(id, request).data
    }

    override fun updateLeadStatus(id: Int, status: String): Flow<ResultState<LeadResponse>> = safeNetworkCall {
        apiService.updateLeadStatus(id, mapOf("status" to status)).data
    }

    override fun deleteLead(id: Int): Flow<ResultState<Unit>> = safeNetworkCall {
        apiService.deleteLead(id)
    }

    override fun getPipeline(
        serviceId: Int?,
        dateFrom: String?,
        dateTo: String?
    ): Flow<ResultState<PipelineResponse>> = safeNetworkCall {
        apiService.getPipeline(serviceId, dateFrom, dateTo)
    }

    override fun getLeadReminders(id: Int): Flow<ResultState<List<LeadReminderResponse>>> = safeNetworkCall {
        apiService.getLeadReminders(id).data
    }

    override fun createLeadReminder(id: Int, request: Map<String, Any?>): Flow<ResultState<LeadReminderResponse>> = safeNetworkCall {
        apiService.createLeadReminder(id, request)
    }

    override fun completeReminder(leadId: Int, reminderId: Int): Flow<ResultState<LeadReminderResponse>> = safeNetworkCall {
        apiService.completeReminder(leadId, reminderId)
    }

    override fun updateReminder(
        leadId: Int,
        reminderId: Int,
        request: Map<String, Any?>
    ): Flow<ResultState<LeadReminderResponse>> = safeNetworkCall {
        apiService.updateReminder(leadId, reminderId, request)
    }

    override fun deleteReminder(leadId: Int, reminderId: Int): Flow<ResultState<Unit>> = safeNetworkCall {
        apiService.deleteReminder(leadId, reminderId)
    }

    override fun getLeadDocuments(id: Int): Flow<ResultState<List<LeadDocumentResponse>>> = safeNetworkCall {
        apiService.getLeadDocuments(id).data
    }

    override fun uploadLeadDocument(id: Int, file: MultipartBody.Part, originalName: String?): Flow<ResultState<LeadDocumentResponse>> = safeNetworkCall {
        apiService.uploadLeadDocument(id, file, originalName)
    }

    override fun deleteLeadDocument(leadId: Int, documentId: Int): Flow<ResultState<Unit>> = safeNetworkCall {
        apiService.deleteLeadDocument(leadId, documentId)
    }

    override fun getLeadActivities(id: Int): Flow<ResultState<List<LeadActivityResponse>>> = safeNetworkCall {
        apiService.getLeadActivities(id).data
    }

    override fun createLeadNote(id: Int, note: String): Flow<ResultState<LeadActivityResponse>> = safeNetworkCall {
        apiService.createLeadNote(id, mapOf("body" to note))
    }
}
