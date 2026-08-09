package com.nusatim.partner.features.leads.ui.shared

import androidx.appcompat.widget.SearchView
import androidx.fragment.app.viewModels
import androidx.fragment.app.Fragment
import androidx.viewpager2.adapter.FragmentStateAdapter
import androidx.viewpager2.widget.ViewPager2
import com.google.android.material.tabs.TabLayoutMediator
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.R
import com.nusatim.partner.features.leads.databinding.FragmentSalesBinding
import com.nusatim.partner.features.leads.ui.customers.CustomersFragment
import com.nusatim.partner.features.leads.ui.leads.LeadsFragment
import com.nusatim.partner.features.leads.ui.pipeline.PipelineFragment
import dagger.hilt.android.AndroidEntryPoint

@AndroidEntryPoint
class SalesFragment : BaseFragment<FragmentSalesBinding>() {

    private val salesViewModel: SalesViewModel by viewModels()

    override fun onInitViews() {
        setupViewPager()
        // setupToolbar() // Toolbar removed per request
    }

    private fun setupViewPager() {
        val adapter = object : FragmentStateAdapter(this) {
            override fun getItemCount(): Int = 3
            override fun createFragment(position: Int): Fragment {
                return when (position) {
                    0 -> PipelineFragment()
                    1 -> LeadsFragment()
                    else -> CustomersFragment()
                }
            }
        }
        binding.viewPager.adapter = adapter

        TabLayoutMediator(binding.tabLayout, binding.viewPager) { tab, position ->
            tab.text = when (position) {
                0 -> "Pipeline"
                1 -> "Lead"
                else -> "Customer"
            }
        }.attach()
    }
}
