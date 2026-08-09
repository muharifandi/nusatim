package com.nusatim.partner.features.leads.ui.leads.detail

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.leads.domain.usecase.*
import com.nusatim.partner.features.leads.ui.leads.detail.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import javax.inject.Inject

@HiltViewModel
class LeadDetailViewModel @Inject constructor(
    private val getLeadDetailUseCase: GetLeadDetailUseCase,
    private val createLeadUseCase: CreateLeadUseCase,
    private val updateLeadUseCase: UpdateLeadUseCase,
    private val updateLeadStatusUseCase: UpdateLeadStatusUseCase,
    private val getLeadRemindersUseCase: GetLeadRemindersUseCase,
    private val completeReminderUseCase: CompleteReminderUseCase,
    private val getLeadActivitiesUseCase: GetLeadActivitiesUseCase,
    private val createLeadNoteUseCase: CreateLeadNoteUseCase,
    private val getLeadDocumentsUseCase: GetLeadDocumentsUseCase,
    private val uploadLeadDocumentUseCase: UploadLeadDocumentUseCase,
    private val deleteLeadUseCase: DeleteLeadUseCase,
    private val getCustomerByLeadIdUseCase: GetCustomerByLeadIdUseCase
) : BaseViewModel<LeadDetailState, LeadDetailIntent, LeadDetailEffect>(LeadDetailState()) {

    override fun processIntent(intent: LeadDetailIntent) {
        when (intent) {
            is LeadDetailIntent.LoadLead -> loadLeadDetail(intent.id)
            is LeadDetailIntent.CreateLead -> createLead(intent.request)
            is LeadDetailIntent.UpdateLead -> updateLead(intent.id, intent.request)
            is LeadDetailIntent.UpdateStatus -> updateStatus(intent.id, intent.status)
            is LeadDetailIntent.LoadReminders -> loadReminders(intent.leadId)
            is LeadDetailIntent.CompleteReminder -> completeReminder(intent.leadId, intent.reminderId)
            is LeadDetailIntent.LoadActivities -> loadActivities(intent.leadId)
            is LeadDetailIntent.AddNote -> addNote(intent.leadId, intent.body)
            is LeadDetailIntent.LoadDocuments -> loadDocuments(intent.leadId)
            is LeadDetailIntent.UploadDocument -> uploadDocument(intent.leadId, intent.file, intent.name)
            is LeadDetailIntent.DeleteLead -> deleteLead(intent.id)
            is LeadDetailIntent.NavigateToCustomer -> findCustomerAndNavigate(intent.leadId)
        }
    }

    fun loadLeadDetail(id: Int) {
        viewModelScope.launch {
            getLeadDetailUseCase(id).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, lead = result.data, error = null) 
                    }
                    is ResultState.Error -> setState { 
                        copy(isLoading = false, error = result.message) 
                    }
                }
            }
        }
    }

    fun createLead(request: Map<String, Any?>) {
        viewModelScope.launch {
            createLeadUseCase(request).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.Success }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    fun updateLead(id: Int, request: Map<String, Any?>) {
        viewModelScope.launch {
            updateLeadUseCase(id, request).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false, lead = result.data) }
                        loadActivities(id) // Skenario #10: refresh timeline after status change
                        sendEffect { LeadDetailEffect.Success }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun updateStatus(id: Int, status: String) {
        viewModelScope.launch {
            updateLeadStatusUseCase(id, status).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false, lead = result.data) }
                        loadActivities(id) // Skenario #10: refresh timeline after status change
                        sendEffect { LeadDetailEffect.Success }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun loadReminders(leadId: Int) {
        viewModelScope.launch {
            getLeadRemindersUseCase(leadId).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, reminders = result.data, error = null) 
                    }
                    is ResultState.Error -> setState { 
                        copy(isLoading = false, error = result.message) 
                    }
                }
            }
        }
    }

    private fun completeReminder(leadId: Int, reminderId: Int) {
        viewModelScope.launch {
            completeReminderUseCase(leadId, reminderId).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        loadReminders(leadId)
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun loadActivities(leadId: Int) {
        viewModelScope.launch {
            getLeadActivitiesUseCase(leadId).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, activities = result.data, error = null) 
                    }
                    is ResultState.Error -> setState { 
                        copy(isLoading = false, error = result.message) 
                    }
                }
            }
        }
    }

    private fun addNote(leadId: Int, body: String) {
        viewModelScope.launch {
            createLeadNoteUseCase(leadId, body).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        loadActivities(leadId)
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun loadDocuments(leadId: Int) {
        viewModelScope.launch {
            getLeadDocumentsUseCase(leadId).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, documents = result.data, error = null) 
                    }
                    is ResultState.Error -> setState { 
                        copy(isLoading = false, error = result.message) 
                    }
                }
            }
        }
    }

    private fun uploadDocument(leadId: Int, file: java.io.File, name: String?) {
        val requestFile = file.asRequestBody("application/octet-stream".toMediaTypeOrNull())
        val body = MultipartBody.Part.createFormData("file", file.name, requestFile)
        
        viewModelScope.launch {
            uploadLeadDocumentUseCase(leadId, body, name).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        loadDocuments(leadId)
                        loadActivities(leadId) // Refresh timeline as well
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun deleteLead(id: Int) {
        viewModelScope.launch {
            deleteLeadUseCase(id).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.Success }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun findCustomerAndNavigate(leadId: Int) {
        viewModelScope.launch {
            getCustomerByLeadIdUseCase(leadId).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.NavigateToCustomerDetail(result.data.id) }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { LeadDetailEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }
}
