package com.nusatim.partner.features.leads.ui.leads.detail.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.LeadActivityResponse
import com.nusatim.partner.core.model.dto.LeadDocumentResponse
import com.nusatim.partner.core.model.dto.LeadReminderResponse
import com.nusatim.partner.core.model.dto.LeadResponse

data class LeadDetailState(
    val isLoading: Boolean = false,
    val lead: LeadResponse? = null,
    val reminders: List<LeadReminderResponse> = emptyList(),
    val activities: List<LeadActivityResponse> = emptyList(),
    val documents: List<LeadDocumentResponse> = emptyList(),
    val error: String? = null
) : UiState
