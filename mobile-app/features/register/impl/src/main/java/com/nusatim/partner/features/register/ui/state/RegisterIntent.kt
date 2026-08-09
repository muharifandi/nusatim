package com.nusatim.partner.features.register.ui.state

import com.nusatim.partner.core.architecture.mvi.UiIntent
import java.io.File

sealed interface RegisterIntent : UiIntent {
    // Stepper
    data object NextStep : RegisterIntent
    data object PreviousStep : RegisterIntent

    // Step 1
    data class NameChanged(val value: String) : RegisterIntent
    data class EmailChanged(val value: String) : RegisterIntent
    data class PasswordChanged(val value: String) : RegisterIntent
    data class PasswordConfirmationChanged(val value: String) : RegisterIntent

    // Step 2
    data class ProfilePhotoPicked(val file: File) : RegisterIntent
    data class KtpPicked(val file: File) : RegisterIntent
    data class NpwpPicked(val file: File?) : RegisterIntent

    // Step 3
    data class BankNameChanged(val value: String) : RegisterIntent
    data class BankAccountNumberChanged(val value: String) : RegisterIntent
    data class BankAccountHolderChanged(val value: String) : RegisterIntent

    // Step 4
    data class AgreementChanged(val value: Boolean) : RegisterIntent

    data object Submit : RegisterIntent
}
