package com.nusatim.partner.features.leads.ui.leads.detail.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface LeadDetailIntent : UiIntent {
    data class LoadLead(val id: Int) : LeadDetailIntent
    data class CreateLead(val request: Map<String, Any?>) : LeadDetailIntent
    data class UpdateLead(val id: Int, val request: Map<String, Any?>) : LeadDetailIntent
    data class UpdateStatus(val id: Int, val status: String) : LeadDetailIntent
    data class LoadReminders(val leadId: Int) : LeadDetailIntent
    data class CompleteReminder(val leadId: Int, val reminderId: Int) : LeadDetailIntent
    data class LoadActivities(val leadId: Int) : LeadDetailIntent
    data class AddNote(val leadId: Int, val body: String) : LeadDetailIntent
    data class LoadDocuments(val leadId: Int) : LeadDetailIntent
    data class UploadDocument(val leadId: Int, val file: java.io.File, val name: String?) : LeadDetailIntent
    data class DeleteLead(val id: Int) : LeadDetailIntent
    data class NavigateToCustomer(val leadId: Int) : LeadDetailIntent
}
