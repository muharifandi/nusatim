package com.nusatim.partner.features.profile.ui.main.state

import com.nusatim.partner.core.architecture.mvi.UiIntent
import java.io.File

sealed interface ProfileIntent : UiIntent {
    data object LoadProfile : ProfileIntent
    data class UpdateProfile(val data: Map<String, Any>) : ProfileIntent
    data class UpdatePhoto(val file: File) : ProfileIntent
    data class UpdateKtp(val file: File) : ProfileIntent
    data class UpdateNpwp(val file: File) : ProfileIntent
    data class UpdatePassword(val current: String, val new: String, val confirm: String) : ProfileIntent
    data object Logout : ProfileIntent
}
