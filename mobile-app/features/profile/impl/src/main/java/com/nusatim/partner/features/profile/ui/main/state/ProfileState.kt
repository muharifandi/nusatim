package com.nusatim.partner.features.profile.ui.main.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.PartnerResponse

data class ProfileState(
    val isLoading: Boolean = false,
    val partner: PartnerResponse? = null,
    val error: String? = null
) : UiState
