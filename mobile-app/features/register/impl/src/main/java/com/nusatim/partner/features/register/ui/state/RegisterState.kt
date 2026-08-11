package com.nusatim.partner.features.register.ui.state

import com.nusatim.partner.core.architecture.mvi.UiState
import java.io.File

data class RegisterState(
    val currentStep: Int = 1,
    val name: String = "",
    val email: String = "",
    val password: String = "",
    val passwordConfirmation: String = "",
    val bankName: String = "",
    val bankAccountNumber: String = "",
    val bankAccountHolder: String = "",
    val profilePhoto: File? = null,
    val ktp: File? = null,
    val npwp: File? = null,
    val isAgreed: Boolean = false,
    val isLoading: Boolean = false,
    val error: String? = null,
    val errorResId: Int? = null,
    val validationErrors: Map<String, List<String>>? = null,
    val isSuccess: Boolean = false
) : UiState {
    val totalSteps = 4
    val progress = (currentStep.toFloat() / totalSteps.toFloat() * 100).toInt()
}
