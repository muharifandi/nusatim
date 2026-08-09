/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : core:architecture
 * File : FeatureApi.kt
 *
 * Description:
 * Interface yang harus diimplementasikan oleh setiap modul fitur untuk mendaftarkan rute navigasinya.
 */

package com.nusatim.partner.core.architecture.navigation

import androidx.navigation.NavGraphBuilder
import androidx.navigation.NavHostController

/**
 * Interface to be implemented by each feature module to register its own navigation routes.
 * This enables distributed navigation and prevents the :app module from being a bottleneck.
 */
interface FeatureApi {
    fun registerGraph(
        navGraphBuilder: NavGraphBuilder,
        navController: NavHostController
    )
}
