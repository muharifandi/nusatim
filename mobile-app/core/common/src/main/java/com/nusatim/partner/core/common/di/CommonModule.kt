/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : core:common
 * File : CommonModule.kt
 *
 * Description:
 * Modul Hilt untuk menyediakan dependensi utilitas umum seperti ConnectivityObserver dan Navigator.
 */

package com.nusatim.partner.core.common.di

import com.nusatim.partner.core.common.navigation.NavigationManager
import com.nusatim.partner.core.common.navigation.Navigator
import com.nusatim.partner.core.common.util.ConnectivityObserver
import com.nusatim.partner.core.common.util.NetworkConnectivityObserver
import dagger.Binds
import dagger.Module
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
abstract class CommonModule {

    @Binds
    @Singleton
    abstract fun bindConnectivityObserver(
        networkConnectivityObserver: NetworkConnectivityObserver
    ): ConnectivityObserver

    @Binds
    @Singleton
    abstract fun bindNavigator(
        navigationManager: NavigationManager
    ): Navigator
}
