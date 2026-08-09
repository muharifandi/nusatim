package com.nusatim.partner.features.finance.ui.commissions.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface CommissionsIntent : UiIntent {
    data class LoadCommissions(val status: String? = null, val page: Int = 1) : CommissionsIntent
    data class LoadCommissionDetail(val id: Int) : CommissionsIntent
}
