package com.nusatim.partner.features.profile.ui.marketing.detail

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.content.Intent
import android.net.Uri
import android.widget.Toast
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.model.dto.MarketingMaterialResponse
import com.nusatim.partner.features.profile.databinding.FragmentMarketingDetailBinding
import com.nusatim.partner.features.profile.ui.marketing.MarketingViewModel
import com.nusatim.partner.features.profile.ui.marketing.state.MarketingIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class MarketingMaterialDetailFragment : BaseFragment<FragmentMarketingDetailBinding>() {

    private val viewModel: MarketingViewModel by viewModels()
    private val materialId by lazy { arguments?.getInt("id") ?: -1 }

    override fun onInitViews() {
        binding.toolbar.setNavigationOnClickListener {
            requireActivity().onBackPressedDispatcher.onBackPressed()
        }

        if (materialId != -1) {
            viewModel.processIntent(MarketingIntent.LoadMarketingMaterialDetail(materialId))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    state.selectedDetail?.let { renderDetail(it) }
                }
            }
        }
    }

    private fun renderDetail(item: MarketingMaterialResponse) {
        binding.tvTitle.text = item.title
        binding.tvCategory.text = item.categoryLabel
        binding.tvDescription.text = item.description ?: ""
        binding.tvDescription.visibility = if (item.description.isNullOrEmpty()) android.view.View.GONE else android.view.View.VISIBLE

        if (item.isFileBased) {
            binding.cardPreview.visibility = android.view.View.VISIBLE
            binding.cardContent.visibility = android.view.View.GONE
            binding.btnPrimaryAction.text = getString(com.nusatim.partner.features.profile.R.string.marketing_btn_download)
            binding.btnPrimaryAction.setOnClickListener {
                val intent = Intent(Intent.ACTION_VIEW, Uri.parse(item.downloadUrl))
                startActivity(intent)
            }
            binding.btnShare.setOnClickListener {
                val intent = Intent(Intent.ACTION_SEND).apply {
                    type = "text/plain"
                    putExtra(Intent.EXTRA_TEXT, "${item.title}: ${item.downloadUrl}")
                }
                startActivity(Intent.createChooser(intent, "Bagikan via"))
            }
        } else {
            binding.cardPreview.visibility = android.view.View.GONE
            binding.cardContent.visibility = android.view.View.VISIBLE
            binding.tvContent.text = item.content
            binding.btnPrimaryAction.text = getString(com.nusatim.partner.features.profile.R.string.marketing_btn_copy)
            binding.btnPrimaryAction.setOnClickListener {
                val clipboard = requireContext().getSystemService(Context.CLIPBOARD_SERVICE) as ClipboardManager
                val clip = ClipData.newPlainText("Marketing Content", item.content)
                clipboard.setPrimaryClip(clip)
                android.widget.Toast.makeText(requireContext(), getString(com.nusatim.partner.features.profile.R.string.marketing_toast_copied), android.widget.Toast.LENGTH_SHORT).show()
            }
            binding.btnShare.setOnClickListener {
                val intent = Intent(Intent.ACTION_SEND).apply {
                    type = "text/plain"
                    putExtra(Intent.EXTRA_TEXT, item.content)
                }
                startActivity(Intent.createChooser(intent, "Bagikan via"))
            }
        }
    }
}
