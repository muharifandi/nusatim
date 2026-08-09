/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : core:common
 * File : NavigationManager.kt
 *
 * Description:
 * Manajer navigasi terpusat yang menggunakan SharedFlow untuk mengirim perintah navigasi antar modul.
 */

package com.nusatim.partner.core.common.navigation

import kotlinx.coroutines.flow.MutableSharedFlow
import kotlinx.coroutines.flow.asSharedFlow
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class NavigationManager @Inject constructor() : Navigator {

    private val _navigationCommands = MutableSharedFlow<NavigationCommand>(extraBufferCapacity = 1)
    val navigationCommands = _navigationCommands.asSharedFlow()

    override fun navigateTo(route: Any) {
        _navigationCommands.tryEmit(NavigationCommand.NavigateTo(route))
    }

    override fun navigateBack() {
        _navigationCommands.tryEmit(NavigationCommand.NavigateBack)
    }

    override fun navigateAndPopUpTo(route: Any, popUpTo: Any, inclusive: Boolean) {
        _navigationCommands.tryEmit(NavigationCommand.NavigateAndPopUpTo(route, popUpTo, inclusive))
    }
}

sealed interface NavigationCommand {
    data class NavigateTo(val route: Any) : NavigationCommand
    data object NavigateBack : NavigationCommand
    data class NavigateAndPopUpTo(val route: Any, val popUpTo: Any, val inclusive: Boolean) : NavigationCommand
}
