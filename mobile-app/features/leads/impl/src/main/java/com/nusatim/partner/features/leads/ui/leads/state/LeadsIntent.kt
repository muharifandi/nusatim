package com.nusatim.partner.features.leads.ui.leads.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface LeadsIntent : UiIntent {
    data class LoadLeads(val page: Int = 1) : LeadsIntent
    data class SearchLeads(val query: String?) : LeadsIntent
    data class FilterLeads(val status: String?) : LeadsIntent
}
