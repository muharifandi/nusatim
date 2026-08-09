package com.nusatim.partner.features.login.ui.forgot

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.login.domain.usecase.ResetPasswordUseCase
import com.nusatim.partner.features.login.ui.forgot.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class ResetPasswordViewModel @Inject constructor(
    private val resetPasswordUseCase: ResetPasswordUseCase
) : BaseViewModel<ResetPasswordState, ResetPasswordIntent, ResetPasswordEffect>(ResetPasswordState()) {

    override fun processIntent(intent: ResetPasswordIntent) {
        when (intent) {
            is ResetPasswordIntent.EmailChanged -> setState { copy(email = intent.value) }
            is ResetPasswordIntent.CodeChanged -> setState { copy(code = intent.value) }
            is ResetPasswordIntent.PasswordChanged -> setState { copy(password = intent.value) }
            is ResetPasswordIntent.PasswordConfirmationChanged -> setState { copy(passwordConfirmation = intent.value) }
            is ResetPasswordIntent.Submit -> submit()
            is ResetPasswordIntent.DismissError -> setState { copy(error = null) }
        }
    }

    private fun submit() {
        val email = state.value.email
        val code = state.value.code
        val password = state.value.password
        val confirmation = state.value.passwordConfirmation

        if (code.isBlank()) {
            setState { copy(error = "Kode reset wajib diisi.") }
            return
        }
        if (password.length < 8) {
            setState { copy(error = "Password minimal 8 karakter.") }
            return
        }
        if (password != confirmation) {
            setState { copy(error = "Konfirmasi password tidak cocok.") }
            return
        }

        viewModelScope.launch {
            val request = mapOf(
                "email" to email,
                "token" to code,
                "password" to password,
                "password_confirmation" to confirmation
            )

            resetPasswordUseCase(request).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true, error = null) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false, isSuccess = true) }
                        sendEffect { ResetPasswordEffect.NavigateToLogin }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                    }
                }
            }
        }
    }
}
