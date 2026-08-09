package com.nusatim.partner.features.profile.ui.support.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface SupportTicketsIntent : UiIntent {
    data class LoadTickets(val page: Int = 1) : SupportTicketsIntent
    data class LoadDetail(val id: Int) : SupportTicketsIntent
    data class CreateTicket(val subject: String, val description: String) : SupportTicketsIntent
}
