/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : core:common
 * File : Navigator.kt
 *
 * Description:
 * Interface abstraksi navigasi (Navigation Bridge Pattern) untuk melepaskan ketergantungan antar modul.
 */

package com.nusatim.partner.core.common.navigation
interface Navigator {
    fun navigateTo(route: Any)
    fun navigateBack()
    fun navigateAndPopUpTo(route: Any, popUpTo: Any, inclusive: Boolean = true)
}
