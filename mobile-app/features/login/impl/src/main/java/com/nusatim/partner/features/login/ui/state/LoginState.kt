package com.nusatim.partner.features.login.ui.state

import com.nusatim.partner.core.architecture.mvi.UiState

data class LoginState(
    val email: String = "",
    val password: String = "",
    val isLoading: Boolean = false,
    val error: String? = null,
    val isSuccess: Boolean = false
) : UiState
